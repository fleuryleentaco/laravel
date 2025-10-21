<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\DocumentError;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','filename','path','mime','size','content','approved'];

    protected $casts = [
        'minhash' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function errors()
    {
        return $this->hasMany(DocumentError::class);
    }
}
