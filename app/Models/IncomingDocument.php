<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingDocument extends Model
{
    use HasFactory;

    protected $fillable = ['remote_id','uploader_id','callback_url','filename','path','mime','size','content','minhash','approved'];

    protected $casts = [
        'minhash' => 'array',
    ];

    public function errors()
    {
        return $this->hasMany(IncomingDocumentError::class);
    }
}
