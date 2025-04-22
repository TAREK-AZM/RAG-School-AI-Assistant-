<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EmbeddingService
{
    /**
     * Generate embedding for a text using Nomic Embed API
     *
     * @param string $text
     * @return string JSON encoded embedding vector
     * @throws \Exception
     */
    public function generateEmbedding($text)
    {
        $cacheKey = 'embedding_' . md5($text);
    
        // Check if the embedding exists in cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
    
        // Make the request to Nomic API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.nomic.api_key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api-atlas.nomic.ai/v1/embedding/text', [
            'model' => 'nomic-embed-text-v1',   // Change this to the appropriate model if necessary
            'texts' => [$text],  // Correct format for input
        ]);
    
        // Check if the request was successful
        if (!$response->successful()) {
            throw new \Exception('Nomic API error: ' . $response->body());
        }
    
        // Get the embedding vector from the response
        $embedding = $response->json()['embeddings'][0];
    
        // Format the embedding as PostgreSQL vector literal (without quotes)
        $formattedEmbedding = '[' . implode(',', $embedding) . ']';
    
        // Store the embedding in cache
        // Cache::put($cacheKey, $formattedEmbedding, now()->addDays(30));
    
        return $formattedEmbedding;
    }
    
}
