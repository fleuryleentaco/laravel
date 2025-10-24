<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentError;
use Illuminate\Support\Facades\Mail;

class ExternalErrorController extends Controller
{
    /**
     * Accept external error reports for a document.
     * No authentication required as per integration contract.
     *
     * PATCH /api/v1/documents/{id}/erreurs
     * Body: { "erreur_trouve": "..." }
     */
    public function report(Request $request, $id)
    {
        $data = $request->validate([
            'erreur_trouve' => 'required|string',
        ]);

        $doc = Document::find($id);
        if (!$doc) {
            return response()->json(['message' => 'Document introuvable'], 404);
        }

        $message = $data['erreur_trouve'];

        // Persist as a DocumentError so it appears in admin and student views
        $error = DocumentError::create([
            'document_id' => $doc->id,
            'user_id' => $doc->user_id,
            'error_type' => 'external',
            'message' => $message,
        ]);

        // Notify the document owner by email (MAIL_MAILER is log in env by default)
        try {
            $user = $doc->user;
            if ($user && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Mail::raw(
                    "Votre document '{$doc->filename}' a reçu des erreurs signalées par un système externe:\n\n" . $message,
                    function ($m) use ($user, $doc) {
                        $m->to($user->email)->subject("Erreurs détectées pour le document {$doc->filename}");
                    }
                );
            }
        } catch (\Throwable $ex) {
            // swallow mail errors for now; endpoint still returns success for record creation
        }

        return response()->json(['message' => 'Erreur enregistrée', 'id' => $error->id], 200);
    }
}
