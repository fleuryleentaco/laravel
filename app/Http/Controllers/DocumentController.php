<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentError;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\TextAnalysis;

class DocumentController extends Controller
{
    use TextAnalysis;
    
    public function index()
    {
        $documents = auth()->user()
            ->documents()
            ->with('errors')
            ->latest()
            ->get();
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240',
        ]);

        $uploaded = [];

        foreach ($request->file('files', []) as $file) {
            $filename = time() . '_' . Str::random(6) . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename);
            $storedPath = storage_path('app/' . $path);
            
            // Extraction du contenu
            $content = $this->extractContentFromPath(
                $file->getMimeType(), 
                $file->extension(), 
                $storedPath
            );

            // Création du document
            $doc = Document::create([
                'user_id' => auth()->id(),
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'content' => $content,
            ]);

            // Calcul et sauvegarde de la signature MinHash si contenu présent
            if ($content) {
                $doc->minhash = $this->computeMinHash($content, 5, 64);
                $doc->save();

                // --- VÉRIFICATION DES RÈGLES ---
                
                // 1. Règles de base (longueur + contenu banni)
                $basicErrors = $this->checkBasicRules($content);
                foreach ($basicErrors as $e) {
                    DocumentError::create([
                        'document_id' => $doc->id,
                        'user_id' => auth()->id(),
                        'error_type' => $e['type'],
                        'message' => $e['message'],
                    ]);
                }

                // 2. Vérification de similarité (seuil 0.5)
                $similarityErrors = $this->checkSimilarity($doc, 0.5);
                foreach ($similarityErrors as $e) {
                    DocumentError::create([
                        'document_id' => $doc->id,
                        'user_id' => auth()->id(),
                        'error_type' => $e['type'],
                        'message' => $e['message'],
                    ]);
                }

                $uploaded[] = [
                    'doc' => $doc, 
                    'errors' => array_merge($basicErrors, $similarityErrors)
                ];
            } else {
                $uploaded[] = ['doc' => $doc, 'errors' => []];
            }
        }

        $message = count($uploaded) > 1 
            ? count($uploaded) . ' fichiers uploadés avec succès' 
            : 'Fichier uploadé avec succès';

        return redirect()->route('documents.index')->with('status', $message);
    }

    public function errors()
    {
        $errors = auth()->user()
            ->documents()
            ->with('errors')
            ->get()
            ->pluck('errors')
            ->flatten();
        return view('documents.errors', compact('errors'));
    }

    public function reportCreate()
    {
        $documents = auth()->user()->documents()->pluck('filename', 'id');
        return view('reports.create', compact('documents'));
    }

    public function reportStore(Request $request)
    {
        $request->validate([
            'document_id' => 'nullable|exists:documents,id',
            'description' => 'required'
        ]);
        
        Report::create([
            'user_id' => auth()->id(),
            'document_id' => $request->document_id,
            'description' => $request->description,
        ]);
        
        return redirect()->route('documents.index')->with('status', 'Rapport soumis avec succès');
    }

    public function analyze(Request $request, $id)
    {
        $doc = Document::findOrFail($id);
        
        // Vérification des permissions
        if (auth()->id() !== $doc->user_id && (auth()->user()->id_role_user ?? 0) != 1) {
            abort(403);
        }

        // Suppression des erreurs précédentes
        DocumentError::where('document_id', $doc->id)->delete();

        if ($doc->content) {
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

            // 2. Vérification de similarité (seuil 0.6 pour analyse manuelle)
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
                ? "Analyse terminée : $totalErrors erreur(s) détectée(s)" 
                : 'Analyse terminée : aucune erreur détectée';
        } else {
            $message = 'Analyse impossible : aucun contenu extrait du document';
        }

        return redirect()->back()->with('status', $message);
    }

    public function download($id)
    {
        $doc = Document::findOrFail($id);
        
        // Vérification des permissions
        if (auth()->id() !== $doc->user_id && (auth()->user()->id_role_user ?? 0) != 1) {
            abort(403);
        }
        
        $path = storage_path('app/' . $doc->path);
        if (!file_exists($path)) {
            abort(404);
        }
        
        return response()->download($path, $doc->filename);
    }

    public function compare($id)
    {
        $doc = Document::findOrFail($id);
        
        // Vérification des permissions
        if (auth()->id() !== $doc->user_id && (auth()->user()->id_role_user ?? 0) != 1) {
            abort(403);
        }

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
                $sa = $this->shinglesText($doc->content, 5);
                $sb = $this->shinglesText($other->content, 5);
                $common = array_keys(array_intersect_key($sa, $sb));
                $snippet = count($common) 
                    ? substr($common[0], 0, 100) . '...' 
                    : substr(strip_tags($other->content), 0, 100) . '...';
                
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
}