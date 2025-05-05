<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentEmbedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'content',
        'embedding',
        'chunk_index',
    ];

    /**
     * Get the document that owns the embedding
     */
    protected $casts = [
        // No need to cast if storing as raw PostgreSQL vector literal
        // 'embedding' => 'array', // Only use if storing as JSON
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
