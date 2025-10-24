<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\IncomingDocument;
use App\Models\IncomingDocumentError;

class ExternalDocumentFetcher
{
    use TextAnalysis;

    /**
     * Fetch documents from configured external API
     * Returns an array with counts: ['imported'=>int,'skipped'=>int]
     */
    public function fetch(int $perPage = 50)
    {
        $base = rtrim(env('EXTERNAL_API_BASE', ''), '/');
        $token = env('EXTERNAL_API_TOKEN');

        if (empty($base)) {
            throw new \Exception('EXTERNAL_API_BASE not configured in .env');
        }

        $page = 1;
        $imported = 0;
        $skipped = 0;

        do {
            // if EXTERNAL_API_BASE already points to the documents endpoint include pagination params
            if (str_contains($base, '/documents')) {
                $url = $base;
            } else {
                $url = $base . '/api/v1/documents';
            }
            $resp = Http::withHeaders(['X-API-Token' => $token])->get($url, ['per_page' => $perPage, 'page' => $page]);
            if (!$resp->ok()) break;
            $data = $resp->json('data', []);
            if (empty($data)) break;

            foreach ($data as $item) {
                $file = $item['fichier'] ?? null;
                if (!$file) continue;

                $original = $file['nom_original'] ?? ($file['nom_stocke'] ?? 'file');
                $size = $file['taille'] ?? null;

                // Skip if already imported (match stored name or filename + size)
                $exists = IncomingDocument::where(function($q) use ($original, $size, $file) {
                    $q->where('filename', $original);
                    if ($size) $q->where('size', $size);
                })->orWhere('filename', $file['nom_stocke'] ?? '')->first();

                if ($exists) {
                    // If record exists but remote_id or callback_url is missing, fill them
                    $updated = false;
                    if (empty($exists->remote_id) && !empty($item['id'])) {
                        $exists->remote_id = $item['id'];
                        $updated = true;
                    }
                    $preferredCallback = $item['callback_url'] ?? $item['callback'] ?? null;
                    if (empty($exists->callback_url) && !empty($preferredCallback)) {
                        $exists->callback_url = $preferredCallback;
                        $updated = true;
                    }
                    if ($updated) {
                        $exists->save();
                        $imported++; // treat as an import/update
                    } else {
                        $skipped++;
                    }
                    continue;
                }

                $downloadUrl = $file['url_download'] ?? null;
                if (!$downloadUrl) {
                    $skipped++;
                    continue;
                }

                $fileResp = Http::withHeaders(['X-API-Token' => $token])->get($downloadUrl);
                if (!$fileResp->ok()) {
                    $skipped++;
                    continue;
                }

                $body = $fileResp->body();
                $mime = $fileResp->header('Content-Type') ?? ($file['extension'] ?? 'application/octet-stream');
                $ext = pathinfo($original, PATHINFO_EXTENSION) ?: explode('/', $mime)[1] ?? 'bin';
                $stored = now()->timestamp . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $original);
                $path = 'incoming/' . $stored;
                Storage::put($path, $body);

                $fullPath = storage_path('app/' . $path);
                $content = $this->extractContentFromPath($mime, strtolower($ext), $fullPath) ?? '';

                // compute minhash
                $minhash = [];
                if ($content) {
                    $minhash = $this->computeMinHash($content, 5, 64);
                }

                $inc = IncomingDocument::create([
                    'remote_id' => $item['id'] ?? null,
                    'uploader_id' => $item['etudiant']['id'] ?? $item['uploader_id'] ?? null,
                    // prefer a callback_url provided by the remote item if present, else fallback to env
                    'callback_url' => $item['callback_url'] ?? $item['callback'] ?? env('EXTERNAL_CALLBACK_URL') ?: null,
                    'filename' => $original,
                    'path' => $path,
                    'mime' => $mime,
                    'size' => $size ?? strlen($body),
                    'content' => $content,
                    'minhash' => $minhash,
                    'approved' => false,
                ]);

                // Run quick checks and persist any errors
                $basic = $this->checkBasicRules($inc->content);
                foreach ($basic as $e) {
                    IncomingDocumentError::create([
                        'incoming_document_id' => $inc->id,
                        'error_type' => $e['type'],
                        'message' => $e['message'],
                    ]);
                }

                // Compare against existing Document and IncomingDocument entries for similarity (both tables)
                $threshold = 0.5;
                $existing = collect();
                $existing = $existing->merge(\App\Models\Document::whereNotNull('content')->get());
                $existing = $existing->merge(\App\Models\IncomingDocument::whereNotNull('content')->where('id','<>',$inc->id)->get());

                foreach ($existing as $other) {
                    if (empty($other->content)) continue;
                    if (empty($other->minhash)) {
                        $other->minhash = $this->computeMinHash($other->content,5,64);
                        $other->save();
                    }
                    $fast = $this->minhashSimilarity($inc->minhash ?? [], $other->minhash ?? []);
                    if ($fast >= 0.4) {
                        $sim = $this->jaccardSimilarityText($inc->content, $other->content, 5);
                    } else {
                        $sim = 0;
                    }
                    if ($sim >= $threshold) {
                        IncomingDocumentError::create([
                            'incoming_document_id' => $inc->id,
                            'error_type' => 'similarity',
                            'message' => "Similaire au document ID {$other->id} ('{$other->filename}') (".round($sim*100,2)."%)",
                        ]);
                    }
                }

                $imported++;
            }

            $page++;
        } while (count($data) == $perPage);

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
