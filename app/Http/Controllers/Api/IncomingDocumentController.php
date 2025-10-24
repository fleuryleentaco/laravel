<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncomingDocument;
use App\Models\IncomingDocumentError;
use Illuminate\Support\Str;
use App\Services\TextAnalysis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IncomingDocumentController extends Controller
{
    use TextAnalysis;

    // Accepts multipart POST: file, uploader_id, callback_url (optional)
    public function store(Request $request)
    {
        // If an API token is configured, require it in X-API-Token header or api_token param
        $requiredToken = env('EXTERNAL_API_TOKEN') ?: env('API_TOKEN');
        if ($requiredToken) {
            $provided = $request->header('X-API-Token') ?? $request->query('api_token');
            if (!$provided || !hash_equals((string)$requiredToken, (string)$provided)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }
        }

        $request->validate([
            'file' => 'required|file|max:20480',
            'uploader_id' => 'required|string',
            'callback_url' => 'nullable|url',
        ]);

        $file = $request->file('file');
        $filename = time().'_'.Str::random(6).'_'.$file->getClientOriginalName();
        $path = $file->storeAs('incoming', $filename);
        $fullPath = storage_path('app/'.$path);
        $content = $this->extractContentFromPath($file->getMimeType(), $file->extension(), $fullPath);

        $doc = IncomingDocument::create([
            'uploader_id' => $request->uploader_id,
            'callback_url' => $request->callback_url,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'content' => $content,
        ]);

        // compute minhash
        if ($content) {
            $sig = $this->computeMinHash($content,5,64);
            $doc->minhash = $sig; $doc->save();
        }

        // run same checks as uploads
        $errors = [];
        if ($content) {
            preg_match_all('/\p{L}+/u', strip_tags($content), $matches);
            $words = count($matches[0] ?? []);
            if ($words < 20) {
                $errors[] = ['type'=>'too_short','message'=>'Document trop court (<20 mots)'];
            }
            $banned = ['loremipsum','plagiarize_example'];
            foreach ($banned as $bad) {
                if (stripos($content,$bad) !== false) {
                    $errors[] = ['type'=>'banned_content','message'=>"Contient la phrase interdite : $bad"];
                }
            }

            // compare to existing incoming documents as well as normal documents
            $threshold = 0.5;
            $existing = array_merge(
                \App\Models\Document::whereNotNull('content')->get()->all(),
                IncomingDocument::whereNotNull('content')->where('id','<>',$doc->id)->get()->all()
            );
            foreach ($existing as $other) {
                if (empty($other->content)) continue;
                if (empty($other->minhash)) {
                    // other may be IncomingDocument or Document
                    $ohash = $this->computeMinHash($other->content,5,64);
                    $other->minhash = $ohash; $other->save();
                }
                $fast = $this->minhashSimilarity($doc->minhash ?? [], $other->minhash ?? []);
                if ($fast >= 0.4) {
                    $sim = $this->jaccardSimilarityText($doc->content, $other->content, 5);
                } else {
                    $sim = 0;
                }
                if ($sim >= $threshold) {
                    $errors[] = ['type'=>'similarity','message'=>"Similaire au document ID {$other->id} (".round($sim*100,2)."%)"];
                }
            }
        }

        foreach ($errors as $e) {
            IncomingDocumentError::create([
                'incoming_document_id' => $doc->id,
                'uploader_id' => $request->uploader_id,
                'error_type' => $e['type'],
                'message' => $e['message'],
            ]);
        }

        // If a callback URL is provided, try to push the errors automatically
        $callback = $doc->callback_url;
        if ($callback && filter_var($callback, FILTER_VALIDATE_URL)) {
            $payload = [
                'uploader_id' => $doc->uploader_id,
                'document_id' => $doc->id,
                'errors' => $doc->errors()->get()->map(fn($e)=>['type'=>$e->error_type,'message'=>$e->message])->toArray(),
            ];

            try {
                // if a secret is configured, sign the payload
                $secret = env('EXTERNAL_CALLBACK_SECRET');
                $json = json_encode($payload);
                $headers = ['Accept' => 'application/json'];
                if ($secret) {
                    $sig = hash_hmac('sha256', $json, $secret);
                    $headers['X-Signature'] = $sig;
                }

                $resp = Http::withHeaders($headers)->post($callback, $payload);
                Log::info('Pushed incoming document errors', ['id'=>$doc->id,'status'=>$resp->status()]);
            } catch (\Throwable $ex) {
                Log::error('Failed to push incoming document errors', ['id'=>$doc->id,'error'=>$ex->getMessage()]);
            }
        }

        return response()->json(['ok'=>true,'id'=>$doc->id,'errors'=>count($errors)], 201);
    }

    // Return errors for an incoming document (for external systems to poll)
    public function errors($id)
    {
        $doc = IncomingDocument::with('errors')->find($id);
        if (!$doc) return response()->json(['ok'=>false,'message'=>'Not found'],404);
        return response()->json([
            'ok' => true,
            'document_id' => $doc->id,
            'uploader_id' => $doc->uploader_id,
            'errors' => $doc->errors->map(fn($e)=>['type'=>$e->error_type,'message'=>$e->message])->toArray(),
        ]);
    }

    // Trigger sending errors to callback_url for a given incoming document (public endpoint as requested)
    public function sendErrors($id)
    {
        $doc = IncomingDocument::with('errors')->find($id);
        if (!$doc) return response()->json(['ok'=>false,'message'=>'Not found'],404);

        if (empty($doc->callback_url)) {
            return response()->json(['ok'=>false,'message'=>'No callback_url configured'],400);
        }

        $payload = [
            'uploader_id' => $doc->uploader_id,
            'document_id' => $doc->id,
            'errors' => $doc->errors->map(fn($e)=>['type'=>$e->error_type,'message'=>$e->message])->toArray(),
        ];

        try {
            $secret = env('EXTERNAL_CALLBACK_SECRET');
            $json = json_encode($payload);
            $headers = ['Accept'=>'application/json'];
            if ($secret) {
                $headers['X-Signature'] = hash_hmac('sha256', $json, $secret);
            }
            $resp = Http::withHeaders($headers)->post($doc->callback_url, $payload);
            return response()->json(['ok'=>true,'status'=>$resp->status()]);
        } catch (\Throwable $ex) {
            return response()->json(['ok'=>false,'message'=>$ex->getMessage()], 500);
        }
    }
}
