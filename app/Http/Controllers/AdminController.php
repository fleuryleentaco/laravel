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

class AdminController extends Controller
{
    use TextAnalysis;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check()) {
                abort(403);
            }
            $user = auth()->user();
            // allow if role id indicates admin (commonly 1)
            if (($user->id_role_user ?? 0) != 1) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function errors()
    {
        $errors = DocumentError::with('document','user')->latest()->get();
        return view('admin.errors', compact('errors'));
    }

    public function sendMessage(Request $request, $errorId)
    {
        $error = DocumentError::findOrFail($errorId);
        // example: send message - for now we just store a report entry or log
        // In a real app, we'd use Mail::to($error->user->email)->send(new ...)
        $error->message = $error->message . "\n\n[Admin note sent] ";
        $error->save();
    return redirect()->back()->with('status','Message envoyé (simulé)');
    }

    public function approveDocument($id)
    {
        $doc = Document::findOrFail($id);
        $doc->approved = true;
        $doc->save();
        // optionally remove errors
        DocumentError::where('document_id',$doc->id)->delete();
    return redirect()->back()->with('status','Document approuvé');
    }

    public function reports()
    {
        $reports = Report::with('user','document')->latest()->get();
        return view('admin.reports', compact('reports'));
    }

    // users management
    public function users()
    {
        $users = User::orderBy('created_at','desc')->paginate(25);
        return view('admin.users', compact('users'));
    }

    public function toggleRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
    // toggle role: if admin(1) -> demote to regular (2), otherwise promote to admin (1)
    $user->id_role_user = ($user->id_role_user == 1) ? 2 : 1;
    $user->save();
    // revoke sessions if demoted
    if(($user->id_role_user ?? 0) != 1){ DB::table('sessions')->where('user_id',$user->id)->delete(); }
        return redirect()->back()->with('status','Role mis à jour');
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // prevent deleting self
        if(auth()->id() == $user->id){ return redirect()->back()->with('error','Impossible de supprimer votre propre compte ici.'); }
        DB::table('sessions')->where('user_id',$user->id)->delete();
        $user->delete();
        return redirect()->back()->with('status','Utilisateur supprimé');
    }

    public function reAnalyze($id)
    {
        $doc = Document::findOrFail($id);
        // re-run simple analysis (same logic as DocumentController::store)
        DocumentError::where('document_id',$doc->id)->delete();
        $errors = [];
        if ($doc->content) {
            $words = str_word_count(strip_tags($doc->content));
            if ($words < 20) {
                $errors[] = ['type' => 'too_short', 'message' => 'Document too short (<20 words)'];
            }
            $banned = ['loremipsum','plagiarize_example'];
            foreach ($banned as $bad) {
                if (stripos($doc->content, $bad) !== false) {
                    $errors[] = ['type' => 'banned_content', 'message' => "Contains banned phrase: $bad"];
                }
            }
            // compute minhash for this document
            $sig = $this->computeMinHash($doc->content, 5, 64);
            $doc->minhash = $sig; $doc->save();
            // similarity check
            $threshold = 0.6;
            $existing = Document::whereNotNull('content')->where('id','<>',$doc->id)->get();
            foreach($existing as $other) {
                $fast = 0;
                if ($other->minhash) {
                    $fast = $this->minhashSimilarity($doc->minhash ?? [], $other->minhash);
                }
                if ($fast >= 0.5) {
                    $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
                } else {
                    $sim = 0;
                }
                if ($sim >= $threshold) {
                    $errors[] = ['type' => 'similarity', 'message' => "Similar to document ID {$other->id} (".round($sim*100,2)."%)"];
                }
            }
        }
        foreach ($errors as $e) {
            DocumentError::create([
                'document_id' => $doc->id,
                'user_id' => $doc->user_id,
                'error_type' => $e['type'],
                'message' => $e['message'],
            ]);
        }
    return redirect()->back()->with('status','Ré-analyse terminée');
    }

    public function compare($id)
    {
        $doc = Document::findOrFail($id);
        $existing = Document::whereNotNull('content')->where('id','<>',$doc->id)->get();
        $results = [];
        foreach ($existing as $other) {
            $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
            if ($sim > 0) {
                // find snippet: first common shingle
                $sa = $this->shinglesText($doc->content,5);
                $sb = $this->shinglesText($other->content,5);
                $common = array_keys(array_intersect_key($sa,$sb));
                $snippet = count($common) ? $common[0] : substr(strip_tags($other->content), 0, 200);
                $results[] = ['other'=>$other,'sim'=>$sim,'snippet'=>$snippet];
            }
        }
        usort($results, fn($a,$b)=>$b['sim']<=>$a['sim']);
        return view('admin.compare', compact('doc','results'));
    }
}
