<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EmbeddingService
{
    /**
     * Generate embedding for a text
     *
     * @param string $text
     * @return string JSON encoded embedding vector
     */
    // public function generateEmbedding($text)
    // {
    //     // Cache key to avoid regenerating embeddings for the same text
    //     $cacheKey = 'embedding_' . md5($text);
        
    //     // Check if we have a cached embedding
    //     if (Cache::has($cacheKey)) {
    //         return Cache::get($cacheKey);
    //     }
        
    //     // Call the embedding API (OpenAI in this example)
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . config('services.openai.api_key'),
    //         'Content-Type' => 'application/json',
    //     ])->post('https://api.openai.com/v1/embeddings', [
    //         'input' => $text,
    //         'model' => config('services.openai.embedding_model', 'text-embedding-ada-002'),
    //     ]);
        
    //     if (!$response->successful()) {
    //         throw new \Exception('Failed to generate embedding: ' . $response->body());
    //     }
        
    //     $embedding = $response->json()['data'][0]['embedding'];
        
    //     // For pgvector, we need to return the embedding as a JSON string
    //     $embeddingJson = json_encode($embedding);
        
    //     // Cache the embedding for future use
    //     Cache::put($cacheKey, $embeddingJson, now()->addDays(30));
        
    //     return $embeddingJson;
    // }

    public function generateEmbedding($text)
{
    // Cache key to avoid regenerating embeddings for the same text
    $cacheKey = 'embedding_' . md5($text);
    
    // Check if we have a cached embedding
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    }
    
    // Call the Groq embedding API
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . config('services.groq.api_key'),
        'Content-Type' => 'application/json',
    ])->post('https://api.groq.com/openai/v1/embeddings', [
        'input' => $text,
        'model' => config('services.groq.embedding_model', 'llama3-embedding-v1'),
    ]);
    
    if (!$response->successful()) {
        throw new \Exception('Failed to generate embedding: ' . $response->body());
    }
    
    $embedding = $response->json()['data'][0]['embedding'];
    
    // For pgvector, we need to return the embedding as a JSON string
    $embeddingJson = json_encode($embedding);
    
    // Cache the embedding for future use
    Cache::put($cacheKey, $embeddingJson, now()->addDays(30));
    
    return $embeddingJson;
}
}