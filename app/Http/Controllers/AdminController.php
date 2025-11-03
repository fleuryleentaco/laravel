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
use App\Models\IncomingDocument;
use App\Models\IncomingDocumentError;
use Illuminate\Support\Facades\Http;

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
        $doc = Document::findOrFail($id);
        $path = storage_path('app/' . $doc->path);
        
        if (!file_exists($path)) {
            abort(404, 'Fichier introuvable sur le serveur');
        }
        
        return response()->download($path, $doc->filename);
    }

    public function toggleRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher de se rétrograder soi-même
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Impossible de modifier votre propre rôle');
        }
        
        // Basculer le rôle : admin(1) ↔ user(3)
        // Note: Les ID de rôles dans la base sont 1 (admin) et 3 (user)
        $newRole = ($user->id_role_user == 1) ? 3 : 1;
        $user->id_role_user = $newRole;
        $user->save();
        
        // Révoquer les sessions si rétrogradé (forcer déconnexion)
        if ($newRole != 1) {
            // Supprimer toutes les sessions de cet utilisateur
            DB::table('sessions')->where('user_id', $user->id)->delete();
            
            // Si l'utilisateur est connecté, le déconnecter
            if (auth()->check() && auth()->id() == $user->id) {
                auth()->logout();
            }
        }
        
        $role = ($newRole == 1) ? 'administrateur' : 'utilisateur';
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
        $doc = Document::findOrFail($id);
        
        if (!$doc->content) {
            return redirect()->back()->with('error', 'Impossible de comparer : aucun contenu extrait');
        }
        
        // S'assurer que le document a une signature
        if (empty($doc->minhash)) {
            $doc->minhash = $this->computeMinHash($doc->content, 5, 64);
            $doc->save();
        }
        
        $existing = Document::whereNotNull('content')
            ->where('id', '<>', $doc->id)
            ->get();
        
        $results = [];
        
        foreach ($existing as $other) {
            if (!$other->content) continue;
            
            // S'assurer que l'autre document a une signature
            if (empty($other->minhash)) {
                $other->minhash = $this->computeMinHash($other->content, 5, 64);
                $other->save();
            }
            
            // Préfiltre MinHash
            $fast = $this->minhashSimilarity(
                $doc->minhash ?? [], 
                $other->minhash ?? []
            );
            
            // Calcul Jaccard si préfiltre passe
            if ($fast >= 0.4) {
                $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
            } else {
                $sim = 0;
            }
            
            // Afficher seulement les similarités > 0
            if ($sim > 0) {
                // Trouver un extrait de texte commun
                $sa = $this->shinglesText($doc->content, 5);
                $sb = $this->shinglesText($other->content, 5);
                $common = array_keys(array_intersect_key($sa, $sb));
                
                $snippet = count($common) 
                    ? substr($common[0], 0, 150) . '...' 
                    : substr(strip_tags($other->content), 0, 150) . '...';
                
                $results[] = [
                    'other' => $other,
                    'sim' => $sim,
                    'snippet' => $snippet
                ];
            }
        }
        
        // Tri par similarité décroissante
        usort($results, fn($a, $b) => $b['sim'] <=> $a['sim']);
        
        return view('admin.compare', compact('doc', 'results'));
    }
    
    public function sendReportResult(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $details = $request->input('details');
        $report->status = 'error_sent';
        $report->admin_response = $details;
        $report->save();
        
        // Optionnel: envoyer une notification à l'utilisateur
        // Mail::to($report->user->email)->send(new ReportResponse($report, $details));
        
        return redirect()->back()->with('status', 'Résultat envoyé avec succès');
    }
    
    /**
     * Liste des documents externes reçus via l'API
     */
    public function incomingDocuments()
    {
        $docs = IncomingDocument::with('errors')
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return view('admin.incoming_documents', compact('docs'));
    }
    
    /**
     * Récupérer les documents depuis une API externe
     */
    public function fetchIncomingDocuments(Request $request)
    {
        try {
            // URL de l'API externe (à configurer dans .env)
            $apiUrl = env('EXTERNAL_API_URL', 'https://api.example.com/documents');
            $apiToken = env('EXTERNAL_API_TOKEN', '');
            
            $response = Http::withToken($apiToken)
                ->timeout(30)
                ->get($apiUrl);
            
            if ($response->successful()) {
                $documents = $response->json('data', []);
                $imported = 0;
                
                foreach ($documents as $doc) {
                    // Vérifier si le document n'existe pas déjà
                    $exists = IncomingDocument::where('external_id', $doc['id'] ?? null)->exists();
                    
                    if (!$exists && isset($doc['filename'])) {
                        IncomingDocument::create([
                            'external_id' => $doc['id'] ?? null,
                            'filename' => $doc['filename'],
                            'content' => $doc['content'] ?? '',
                            'uploader_id' => $doc['uploader_id'] ?? 'unknown',
                            'metadata' => json_encode($doc),
                        ]);
                        $imported++;
                    }
                }
                
                return redirect()->back()->with('status', "$imported document(s) importé(s) avec succès");
            } else {
                return redirect()->back()->with('error', 'Erreur lors de la récupération des documents: ' . $response->status());
            }
        } catch (\Exception $e) {
            \Log::error('Erreur fetch incoming documents: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    /**
     * Comparer un document externe
     */
    public function compareIncomingDocument($id)
    {
        $doc = IncomingDocument::findOrFail($id);
        
        if (!$doc->content) {
            return redirect()->back()->with('error', 'Impossible de comparer : aucun contenu');
        }
        
        // Calculer la signature MinHash si nécessaire
        if (empty($doc->minhash)) {
            $doc->minhash = $this->computeMinHash($doc->content, 5, 64);
            $doc->save();
        }
        
        // Comparer avec tous les documents internes
        $existing = Document::whereNotNull('content')->get();
        $results = [];
        
        foreach ($existing as $other) {
            if (!$other->content) continue;
            
            if (empty($other->minhash)) {
                $other->minhash = $this->computeMinHash($other->content, 5, 64);
                $other->save();
            }
            
            $fast = $this->minhashSimilarity(
                $doc->minhash ?? [], 
                $other->minhash ?? []
            );
            
            if ($fast >= 0.4) {
                $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
            } else {
                $sim = 0;
            }
            
            if ($sim > 0) {
                $sa = $this->shinglesText($doc->content, 5);
                $sb = $this->shinglesText($other->content, 5);
                $common = array_keys(array_intersect_key($sa, $sb));
                
                $snippet = count($common) 
                    ? substr($common[0], 0, 150) . '...' 
                    : substr(strip_tags($other->content), 0, 150) . '...';
                
                $results[] = [
                    'other' => $other,
                    'sim' => $sim,
                    'snippet' => $snippet
                ];
            }
        }
        
        usort($results, fn($a, $b) => $b['sim'] <=> $a['sim']);
        
        return view('admin.incoming_compare', compact('doc', 'results'));
    }
    
    /**
     * Envoyer les erreurs d'un document externe
     */
    public function sendIncomingErrors(Request $request, $id)
    {
        $doc = IncomingDocument::findOrFail($id);
        
        // Récupérer les erreurs
        $errors = IncomingDocumentError::where('incoming_document_id', $doc->id)->get();
        
        // Préparer la réponse
        $errorData = [
            'document_id' => $doc->external_id,
            'filename' => $doc->filename,
            'errors' => $errors->map(function($e) {
                return [
                    'type' => $e->error_type,
                    'message' => $e->message,
                ];
            })->toArray(),
            'timestamp' => now()->toIso8601String(),
        ];
        
        // Envoyer à l'API externe
        try {
            $callbackUrl = $doc->callback_url ?? env('EXTERNAL_API_CALLBACK_URL');
            
            if ($callbackUrl) {
                $response = Http::timeout(30)->post($callbackUrl, $errorData);
                
                if ($response->successful()) {
                    return redirect()->back()->with('status', 'Erreurs envoyées avec succès');
                } else {
                    return redirect()->back()->with('error', 'Erreur lors de l\'envoi: ' . $response->status());
                }
            } else {
                return redirect()->back()->with('error', 'URL de callback non configurée');
            }
        } catch (\Exception $e) {
            \Log::error('Erreur envoi erreurs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}