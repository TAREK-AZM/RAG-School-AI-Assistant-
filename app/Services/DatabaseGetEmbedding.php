<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;


class DatabaseGetEmbedding
{
    

    public static function findRelevantDocuments($embedding)
    {
        $limit = config('vectordb.top_k', 5);
        $threshold = config('vectordb.similarity_threshold', 0.7);
        
        $query = DB::table('document_embeddings')
            ->select([
                'document_embeddings.id',
                'document_embeddings.content',
                'documents.title',
                'documents.category',
                DB::raw("1 - (embedding <=> '$embedding'::vector) as similarity")
            ])
            ->join('documents', 'document_embeddings.document_id', '=', 'documents.id')
            ->where('documents.status', 'completed');
        
        
        
        $results = $query->orderByRaw("embedding <=> '$embedding'::vector")
            ->limit($limit)
            ->get();
            
        // Filter out low similarity results
        return $results->filter(function ($item) use ($threshold) {
            return $item->similarity >= $threshold;
        })->all();
    }
}