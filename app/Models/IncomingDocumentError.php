<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingDocumentError extends Model
{
    use HasFactory;

    protected $fillable = ['incoming_document_id','uploader_id','error_type','message'];

    public function document()
    {
        return $this->belongsTo(IncomingDocument::class, 'incoming_document_id');
    }
}
