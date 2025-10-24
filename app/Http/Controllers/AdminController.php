<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentError;
use App\Models\Report;
use Illuminate\Support\Facades\Mail;
use App\Services\TextAnalysis;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    use TextAnalysis;
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                abort(403, 'Non authentifié');
            }
            $user = auth()->user();
            if (($user->id_role_user ?? 0) != 1) {
                abort(403, 'Accès réservé aux administrateurs');
            }
            return $next($request);
        });
    }

    public function errors()
    {
        $errors = DocumentError::with('document', 'user')
            ->latest()
            ->paginate(50);
        return view('admin.errors', compact('errors'));
    }

    public function sendMessage(Request $request, $errorId)
    {
        $error = DocumentError::findOrFail($errorId);
        
        // Dans une vraie application, envoyer un email
        // Mail::to($error->user->email)->send(new ErrorNotification($error));
        
        $error->message = $error->message . "\n\n[Note admin envoyée le " . now()->format('d/m/Y H:i') . "]";
        $error->save();
        
        return redirect()->back()->with('status', 'Message envoyé (simulé)');
    }

    public function approveDocument($id)
    {
        $doc = Document::findOrFail($id);
        $doc->approved = true;
        $doc->save();
        
        // Optionnellement supprimer les erreurs
        DocumentError::where('document_id', $doc->id)->delete();
        
        return redirect()->back()->with('status', "Document '{$doc->filename}' approuvé avec succès");
    }

    public function reports()
    {
        $reports = Report::with('user', 'document')
            ->latest()
            ->paginate(30);
        return view('admin.reports', compact('reports'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.users', compact('users'));
    }

    public function documents()
    {
        $documents = Document::with('user', 'errors')
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return view('admin.documents', compact('documents'));
    }

    public function download($id)
    {
        // try Document first, then IncomingDocument
        $doc = Document::find($id);
        if ($doc) {
            $path = storage_path('app/' . $doc->path);
            if (!file_exists($path)) abort(404, 'Fichier introuvable sur le serveur');
            return response()->download($path, $doc->filename);
        }

        $inc = \App\Models\IncomingDocument::findOrFail($id);
        $path = storage_path('app/' . $inc->path);
        if (!file_exists($path)) abort(404, 'Fichier introuvable sur le serveur');
        return response()->download($path, $inc->filename);
    }

    public function toggleRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher de se rétrograder soi-même
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Impossible de modifier votre propre rôle');
        }
        
        // Basculer le rôle : admin(1) ↔ user(2)
        $user->id_role_user = ($user->id_role_user == 1) ? 2 : 1;
        $user->save();
        
        // Révoquer les sessions si rétrogradé
        if (($user->id_role_user ?? 0) != 1) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
        
        $role = ($user->id_role_user == 1) ? 'administrateur' : 'utilisateur';
        return redirect()->back()->with('status', "Rôle mis à jour : $role");
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher de se supprimer soi-même
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Impossible de supprimer votre propre compte');
        }
        
        // Supprimer les sessions
        DB::table('sessions')->where('user_id', $user->id)->delete();
        
        // Supprimer l'utilisateur (les documents seront orphelins ou supprimés selon cascade)
        $user->delete();
        
        return redirect()->back()->with('status', 'Utilisateur supprimé avec succès');
    }

    public function reAnalyze($id)
    {
        $doc = Document::findOrFail($id);
        
        if (!$doc->content) {
            return redirect()->back()->with('error', 'Impossible d\'analyser : aucun contenu extrait');
        }
        
        // Suppression des erreurs précédentes
        DocumentError::where('document_id', $doc->id)->delete();
        
        // Calcul/mise à jour de la signature MinHash
        $doc->minhash = $this->computeMinHash($doc->content, 5, 64);
        $doc->save();
        
        // 1. Vérification des règles de base
        $basicErrors = $this->checkBasicRules($doc->content);
        foreach ($basicErrors as $e) {
            DocumentError::create([
                'document_id' => $doc->id,
                'user_id' => $doc->user_id,
                'error_type' => $e['type'],
                'message' => $e['message'],
            ]);
        }
        
        // 2. Vérification de similarité (seuil 0.6)
        $similarityErrors = $this->checkSimilarity($doc, 0.6);
        foreach ($similarityErrors as $e) {
            DocumentError::create([
                'document_id' => $doc->id,
                'user_id' => $doc->user_id,
                'error_type' => $e['type'],
                'message' => $e['message'],
            ]);
        }
        
        $totalErrors = count($basicErrors) + count($similarityErrors);
        $message = $totalErrors > 0 
            ? "Ré-analyse terminée : $totalErrors erreur(s) détectée(s)" 
            : 'Ré-analyse terminée : aucune erreur détectée';
        
        return redirect()->back()->with('status', $message);
    }

    public function compare($id)
    {
        // support comparing Document or IncomingDocument
        $doc = Document::find($id);
        $isIncoming = false;
        if (!$doc) {
            $doc = \App\Models\IncomingDocument::findOrFail($id);
            $isIncoming = true;
        }

        if (!$doc->content) {
            // try to extract content from stored file if present
            $path = $doc->path ?? null;
            if ($path) {
                $full = storage_path('app/' . $path);
                if (file_exists($full)) {
                    $ext = pathinfo($doc->filename ?? $path, PATHINFO_EXTENSION);
                    $doc->content = $this->extractContentFromPath($doc->mime ?? '', strtolower($ext), $full) ?? $doc->content;
                    if ($doc->content) $doc->save();
                }
            }
        }

        if (!$doc->content) {
            return redirect()->back()->with('error', 'Impossible de comparer : aucun contenu extrait');
        }

        // apply basic rules to the document and persist any new errors
        $detectedErrors = [];
        $basic = $this->checkBasicRules($doc->content);
        foreach ($basic as $e) {
            $exists = \App\Models\DocumentError::where('document_id', $doc->id)->where('error_type', $e['type'])->where('message', $e['message'])->first();
            if (!$exists) {
                \App\Models\DocumentError::create([
                    'document_id' => $doc->id,
                    'user_id' => $doc->user_id,
                    'error_type' => $e['type'],
                    'message' => $e['message'],
                ]);
            }
            $detectedErrors[] = $e;
        }

        if (empty($doc->minhash)) {
            $doc->minhash = $this->computeMinHash($doc->content, 5, 64);
            $doc->save();
        }

        // collect existing documents from both tables
        $existing = collect();
        $existing = $existing->merge(Document::whereNotNull('content')->get());
        $existing = $existing->merge(\App\Models\IncomingDocument::whereNotNull('content')->get());
        // exclude the current by id & type
        $existing = $existing->filter(fn($o) => !(($isIncoming && $o instanceof \App\Models\IncomingDocument && $o->id == $doc->id) || (!$isIncoming && $o instanceof Document && $o->id == $doc->id)));
        
        $results = [];

        foreach ($existing as $other) {
            if (!$other->content) continue;

            if (empty($other->minhash)) {
                $other->minhash = $this->computeMinHash($other->content, 5, 64);
                $other->save();
            }

            $fast = $this->minhashSimilarity($doc->minhash ?? [], $other->minhash ?? []);
            if ($fast >= 0.4) {
                $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
            } else {
                $sim = 0;
            }

            if ($sim > 0) {
                $sa = $this->shinglesText($doc->content, 5);
                $sb = $this->shinglesText($other->content, 5);
                $common = array_keys(array_intersect_key($sa, $sb));
                $snippet = count($common) ? substr($common[0], 0, 150) . '...' : substr(strip_tags($other->content), 0, 150) . '...';
                $results[] = ['other' => $other, 'sim' => $sim, 'snippet' => $snippet];

                // persist similarity error for this document
                $msg = "Similaire au document ID {$other->id} ('{$other->filename}') (".round($sim*100,2)."%)";
                $existsSim = \App\Models\DocumentError::where('document_id', $doc->id)->where('error_type', 'similarity')->where('message', $msg)->first();
                if (!$existsSim) {
                    \App\Models\DocumentError::create([
                        'document_id' => $doc->id,
                        'user_id' => $doc->user_id,
                        'error_type' => 'similarity',
                        'message' => $msg,
                    ]);
                }
                $detectedErrors[] = ['type'=>'similarity','message'=>$msg];
            }
        }
        
        // Tri par similarité décroissante
        usort($results, fn($a, $b) => $b['sim'] <=> $a['sim']);
        
        return view('admin.compare', compact('doc', 'results', 'detectedErrors'));
    }

    // show incoming documents (from external systems)
    public function incomingDocuments()
    {
        $docs = \App\Models\IncomingDocument::with('errors')->orderBy('created_at','desc')->paginate(25);
        return view('admin.incoming_documents', compact('docs'));
    }

    // Compare an incoming document (dedicated to avoid id collisions)
    public function compareIncoming($id)
    {
        $doc = \App\Models\IncomingDocument::findOrFail($id);

        if (!$doc->content) {
            // attempt to extract from stored file
            $path = $doc->path ?? null;
            if ($path) {
                $full = storage_path('app/' . $path);
                if (file_exists($full)) {
                    $ext = pathinfo($doc->filename ?? $path, PATHINFO_EXTENSION);
                    $doc->content = $this->extractContentFromPath($doc->mime ?? '', strtolower($ext), $full) ?? $doc->content;
                    if ($doc->content) $doc->save();
                }
            }
        }

        if (!$doc->content) {
            return redirect()->back()->with('error', 'Impossible de comparer : aucun contenu extrait');
        }

        // apply basic rules and persist incoming errors
        $detectedErrors = [];
        $basic = $this->checkBasicRules($doc->content);
        foreach ($basic as $e) {
            $exists = \App\Models\IncomingDocumentError::where('incoming_document_id', $doc->id)->where('error_type', $e['type'])->where('message', $e['message'])->first();
            if (!$exists) {
                \App\Models\IncomingDocumentError::create([
                    'incoming_document_id' => $doc->id,
                    'error_type' => $e['type'],
                    'message' => $e['message'],
                ]);
            }
            $detectedErrors[] = $e;
        }

        if (empty($doc->minhash)) {
            $doc->minhash = $this->computeMinHash($doc->content, 5, 64);
            $doc->save();
        }

        // collect existing documents from both tables
        $existing = collect();
        $existing = $existing->merge(\App\Models\Document::whereNotNull('content')->get());
        $existing = $existing->merge(\App\Models\IncomingDocument::whereNotNull('content')->where('id','<>',$doc->id)->get());

        $results = [];

        foreach ($existing as $other) {
            if (!$other->content) continue;

            if (empty($other->minhash)) {
                $other->minhash = $this->computeMinHash($other->content, 5, 64);
                $other->save();
            }

            $fast = $this->minhashSimilarity($doc->minhash ?? [], $other->minhash ?? []);
            if ($fast >= 0.4) {
                $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
            } else {
                $sim = 0;
            }

            if ($sim > 0) {
                $sa = $this->shinglesText($doc->content, 5);
                $sb = $this->shinglesText($other->content, 5);
                $common = array_keys(array_intersect_key($sa, $sb));
                $snippet = count($common) ? substr($common[0], 0, 150) . '...' : substr(strip_tags($other->content), 0, 150) . '...';
                $results[] = ['other' => $other, 'sim' => $sim, 'snippet' => $snippet];

                // persist similarity error for this incoming document
                $msg = "Similaire au document ID {$other->id} ('{$other->filename}') (".round($sim*100,2)."%)";
                $existsSim = \App\Models\IncomingDocumentError::where('incoming_document_id', $doc->id)->where('error_type', 'similarity')->where('message', $msg)->first();
                if (!$existsSim) {
                    \App\Models\IncomingDocumentError::create([
                        'incoming_document_id' => $doc->id,
                        'error_type' => 'similarity',
                        'message' => $msg,
                    ]);
                }
                $detectedErrors[] = ['type'=>'similarity','message'=>$msg];
            }
        }

        usort($results, fn($a, $b) => $b['sim'] <=> $a['sim']);

        return view('admin.compare', compact('doc', 'results', 'detectedErrors'));
    }

    // send detected errors back to external system via callback_url
    public function sendIncomingErrors(Request $request, $id)
    {
        $doc = \App\Models\IncomingDocument::with('errors')->findOrFail($id);
        // Build a set of candidate callback URLs. Preference order:
        // 1) document-provided callback_url
        // 2) EXTERNAL_CALLBACK_URL env
        // 3) derived from EXTERNAL_API_BASE + remote_id (if present)

        $candidates = [];
        if (!empty($doc->callback_url)) $candidates[] = $doc->callback_url;
        if (!empty(env('EXTERNAL_CALLBACK_URL'))) $candidates[] = env('EXTERNAL_CALLBACK_URL');

        // If we imported the remote id and EXTERNAL_API_BASE is set, try to build a reasonable callback
        $externalBase = rtrim(env('EXTERNAL_API_BASE', ''), '/');
        if (!empty($externalBase) && !empty($doc->remote_id)) {
            // EXTERNAL_API_BASE typically points to something like http://10.235.242.51:8000/api/v1/documents
            // We'll append /{remote_id}/errors to target a sensible errors endpoint on the remote system.
            $candidates[] = $externalBase . '/' . $doc->remote_id . '/errors';
        }

        // last-resort: if EXTERNAL_API_BASE is the host only (no /documents), try a common path
        if (!empty($externalBase) && strpos($externalBase, '/documents') === false && !empty($doc->remote_id)) {
            $candidates[] = $externalBase . '/api/v1/documents/' . $doc->remote_id . '/errors';
        }

        // Substitute common placeholders in candidate URLs so admins can set EXTERNAL_CALLBACK_URL
        // like "http://10.235.242.51:8000/api/v1/documents/{id}/erreurs"
        $replaceWith = $doc->remote_id ?? $doc->id;
        $replacements = [
            '{id}' => $replaceWith,
            '{remote_id}' => $replaceWith,
            '{document_id}' => $replaceWith,
            '{local_id}' => $doc->id,
            '{incoming_id}' => $doc->id,
        ];
        $processed = [];
        foreach ($candidates as $c) {
            $tmp = $c;
            foreach ($replacements as $k => $v) {
                if ($v !== null) {
                    $tmp = str_replace($k, $v, $tmp);
                }
            }
            $processed[] = $tmp;
        }

        $callback = null;
        foreach ($processed as $c) {
            if (filter_var($c, FILTER_VALIDATE_URL)) { $callback = $c; break; }
        }

        if (empty($callback)) {
            return redirect()->back()->with('error','Aucune callback URL trouvée/configurée pour ce document (essayez de configurer EXTERNAL_CALLBACK_URL ou assurez-vous que le document contient callback_url)');
        }

        // Build a concise French message expected by the remote integration per their docs
        $errorsArr = $doc->errors->map(fn($e) => $e->message)->toArray();
        $erreur_trouve = count($errorsArr) ? "Les erreurs suivantes ont été détectées:\n" . implode("\n", $errorsArr) : "Aucune erreur détectée";

        // Payload the remote docs expect: { "erreur_trouve": "..." }
        $payload = [
            'erreur_trouve' => $erreur_trouve,
        ];

        try {
            $token = env('EXTERNAL_API_TOKEN') ?: env('API_TOKEN');
            // Do not send token by default unless explicitly configured (you indicated tokens are not needed)
            $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
            if ($token) $headers['X-API-Token'] = $token;

            // Try the documented method first: PATCH to /erreurs (or the candidate callback)
            $variants = [$callback];
            if (str_ends_with($callback, '/errors')) {
                $variants[] = preg_replace('#/errors$#', '/erreurs', $callback);
            }
            if (str_ends_with($callback, '/erreurs')) {
                $variants[] = preg_replace('#/erreurs$#', '/errors', $callback);
            }

            $attempts = [];
            foreach (array_unique($variants) as $url) {
                // prefer the French /erreurs first if present
                if (str_ends_with($url, '/erreurs')) {
                    $attempts[] = ['method' => 'patch', 'url' => $url];
                    $attempts[] = ['method' => 'put', 'url' => $url];
                    $attempts[] = ['method' => 'post', 'url' => $url];
                    // also try the english variant afterwards
                    $eng = preg_replace('#/erreurs$#', '/errors', $url);
                    $attempts[] = ['method' => 'patch', 'url' => $eng];
                    $attempts[] = ['method' => 'post', 'url' => $eng];
                } else {
                    // if url ends with /errors, try /erreurs first
                    if (str_ends_with($url, '/errors')) {
                        $fr = preg_replace('#/errors$#', '/erreurs', $url);
                        $attempts[] = ['method' => 'patch', 'url' => $fr];
                        $attempts[] = ['method' => 'patch', 'url' => $url];
                    } else {
                        $attempts[] = ['method' => 'patch', 'url' => $url];
                    }
                    $attempts[] = ['method' => 'put', 'url' => $url];
                    $attempts[] = ['method' => 'post', 'url' => $url];
                }
            }

            $lastResp = null;
            $lastEx = null;
            foreach ($attempts as $a) {
                $method = $a['method'];
                $url = $a['url'];
                try {
                    // send as JSON body to match curl exactly
                    $resp = \Illuminate\Support\Facades\Http::withHeaders($headers)->send(strtoupper($method), $url, ['json' => $payload]);
                    // log attempt
                    Log::info('Callback attempt', ['method' => $method, 'url' => $url, 'status' => $resp->status(), 'body' => substr($resp->body(), 0, 500)]);
                    $lastResp = $resp;
                    if ($resp->successful()) {
                        return redirect()->back()->with('status','Erreurs envoyées au système externe (' . parse_url($url, PHP_URL_HOST) . ', method=' . strtoupper($method) . ')');
                    }
                    // if 405 or 404, continue to next variant
                } catch (\Throwable $ex) {
                    $lastEx = $ex;
                    Log::error('Callback exception', ['method' => $method, 'url' => $url, 'exception' => $ex->getMessage()]);
                }
            }

            // Return a helpful message including last response status and body (if available)
            if ($lastResp) {
                $body = $lastResp->body();
                return redirect()->back()->with('error','Envoi échoué, dernière réponse: '.$lastResp->status().' - '.substr($body,0,200));
            }
            if ($lastEx) {
                return redirect()->back()->with('error','Erreur d’envoi: '.$lastEx->getMessage());
            }
            return redirect()->back()->with('error','Envoi échoué: aucune réponse du remote');
        } catch (\Throwable $ex) {
            return redirect()->back()->with('error','Erreur d’envoi: '.$ex->getMessage());
        }
    }
    
    /**
     * Manually fetch documents from configured external API and import them as IncomingDocument
     */
    public function fetchIncomingFromApi(Request $request)
    {
        try {
            $fetcher = new \App\Services\ExternalDocumentFetcher();
            $result = $fetcher->fetch();
            $count = $result['imported'] ?? 0;
            $skipped = $result['skipped'] ?? 0;
            return redirect()->route('admin.incoming')->with('status', "Import terminé : $count nouveau(x) document(s), $skipped ignoré(s)");
        } catch (\Throwable $ex) {
            return redirect()->route('admin.incoming')->with('error', 'Erreur lors de la récupération : ' . $ex->getMessage());
        }
    }
    
    public function sendReportResult(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $details = $request->input('details');
        $report->status = 'error_sent';
        $report->save();
        // Send notification to the user with file name and report description
        $fileName = $report->document->filename ?? 'Document supprimé';
        $reportDescription = $report->description;
        $report->user->notify(new \App\Notifications\ReportErrorResult($fileName, $reportDescription, $details));
        return redirect()->back()->with('status', 'Résultat envoyé à l’utilisateur : ' . $details);
    }
}