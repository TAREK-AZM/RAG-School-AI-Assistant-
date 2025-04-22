<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'filename',
        'filepath',
        'status',
        'chunk_count',
        'processed_at',
        'uploaded_by',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Get the embeddings for the document
     */
    public function embeddings()
    {
        return $this->hasMany(DocumentEmbedding::class);
    }
}
