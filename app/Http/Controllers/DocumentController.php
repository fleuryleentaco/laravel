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
        $documents = auth()->user()->documents()->latest()->get();
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
            $filename = time().'_'.Str::random(6).'_'.$file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename);
            $storedPath = storage_path('app/'.$path);
            $content = $this->extractContentFromPath($file->getMimeType(), $file->extension(), $storedPath);

            $doc = Document::create([
                'user_id' => auth()->id(),
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'content' => $content,
            ]);

            // compute and save minhash signature
            if ($content) {
                $sig = $this->computeMinHash($content, 5, 64);
                $doc->minhash = $sig;
                $doc->save();
            }

                // similarity check vs existing documents
                if ($content) {
                    $threshold = 0.6; // similarity threshold
                    $existing = Document::whereNotNull('content')->where('id','<>',$doc->id)->get();
                    foreach($existing as $other) {
                        // quick check by minhash if available
                        $fast = 0;
                        if ($other->minhash && $doc->minhash) {
                            $fast = $this->minhashSimilarity($doc->minhash, $other->minhash);
                        }
                        // if fast similarity suggests possible match, do detailed jaccard
                        if ($fast >= 0.5) {
                            $sim = $this->jaccardSimilarityText($content, $other->content, 5);
                        } else {
                            $sim = 0;
                        }
                        if ($sim >= $threshold) {
                            DocumentError::create([
                                'document_id' => $doc->id,
                                'user_id' => auth()->id(),
                                'error_type' => 'similarity',
                                'message' => "Similar to document ID {$other->id} (".round($sim*100,2)."%)",
                            ]);
                        }
                    }
                }

            // simple analysis: check rules (example: content length < 20 words => 'too_short', or contains banned words)
            $errors = [];
            if ($content) {
                $words = str_word_count(strip_tags($content));
                if ($words < 20) {
                    $errors[] = ['type' => 'too_short', 'message' => 'Document too short (<20 words)'];
                }
                // banned words example
                $banned = ['loremipsum','plagiarize_example'];
                foreach ($banned as $bad) {
                    if (stripos($content, $bad) !== false) {
                        $errors[] = ['type' => 'banned_content', 'message' => "Contains banned phrase: $bad"];
                    }
                }
            }

            // If errors found, save DocumentError entries
            foreach ($errors as $e) {
                DocumentError::create([
                    'document_id' => $doc->id,
                    'user_id' => auth()->id(),
                    'error_type' => $e['type'],
                    'message' => $e['message'],
                ]);
            }

            $uploaded[] = ['doc' => $doc, 'errors' => $errors];
        }

        return redirect()->route('documents.index')->with('status', 'Fichiers uploadés.');
    }

    // text extraction and similarity logic moved to App\Services\TextAnalysis trait

    public function errors()
    {
        $errors = auth()->user()->documents()->with('errors')->get()->pluck('errors')->flatten();
        return view('documents.errors', compact('errors'));
    }

    public function reportCreate()
    {
        $documents = auth()->user()->documents()->pluck('filename','id');
        return view('reports.create', compact('documents'));
    }

    public function reportStore(Request $request)
    {
        $request->validate(['document_id' => 'nullable|exists:documents,id','description'=>'required']);
        Report::create([
            'user_id'=>auth()->id(),
            'document_id'=>$request->document_id,
            'description'=>$request->description,
        ]);
    return redirect()->route('documents.index')->with('status','Rapport soumis');
    }
}
