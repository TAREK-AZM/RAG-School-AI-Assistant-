<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\AiModelsProdviders\GeminiAiModelParams;
USE App\Services\promts\AnswerPromts;

class GeminiEmbeddingService implements AIServiceInterface
{
   

    public function generate_Answer($Text, $questionEmbedding)
{
    $relevantDocs = DatabaseGetEmbedding::findRelevantDocuments($questionEmbedding);
    
    // Step 1: Build the context block from documents
    $context = "You have access to the following information from official school documents:\n\n";

    foreach ($relevantDocs as $doc) {
        $context .= "Document: {$doc->content}\n";
    }

    // Step 2: Prepare the refined system prompt
    $systemPrompt = AnswerPromts::NOMIC_PROMT_ANSWER_QUESTION;

    // Step 3: Call the Gemini API
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=".config('services.gemini.api_key');
    
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post($url, [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $systemPrompt . "\n\nQuestion: " . $Text . "\n\nContext: \n\n" . $context
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 500
        ]
    ]);

    // Step 4: Handle errors gracefully
    if (!$response->successful()) {
        return "I'm sorry, I encountered an error generating a response. Please try again later.\n\n" . $response->body();
    }

    // Step 5: Return the assistant's answer
    $responseData = $response->json();
    return $responseData['candidates'][0]['content']['parts'][0]['text'];
}




    public function generate_Embedding(
        $Text,
        $EmbeddingModel = GeminiAiModelParams::EMBEDDING_MODEL_EXP_03_07, 
        $TaskType = GeminiAiModelParams::TASK_TYPE_RETRIEVAL_DOCUMENT,  
        $Dimention = GeminiAiModelParams::EMBED_DIM_EXP_03_07_768, 
        $temperature = GeminiAiModelParams::TEXT_TEMPERATURE_DEFAULT, 
        $max_tokens = GeminiAiModelParams::MAX_TOKENS_V_500)
     {
        // Build a unique cache key
        $cacheKey = "gemini_embedding_{$TaskType}_{$Dimention}_" . md5($Text);
        
        // Check cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Prepare the API request
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$EmbeddingModel}:embedContent?key=" 
        . config('services.gemini.api_key');
        
        $payload = [
            'content' => [
                'parts' => [
                    ['text' => $Text]
                ]
            ],
            'taskType' => $TaskType,
            'outputDimensionality' => $Dimention
        ];
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $payload);
        
        if (!$response->successful()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }
        
        $embedding = $response->json()['embedding']["values"];
        $formatted = '[' . implode(',', $embedding) . ']'; // PGvector literal

        // Cache the result for 30 days
        Cache::put($cacheKey, $formatted, now()->addDays(30));
        
        return $formatted;
    }

    public function generate_Text(
        $Text,
        $EmbeddingModel,
        $TaskType,
        $Dimention,
        $temperature, 
        $max_tokens) 
    {

    }





    // public function generateEmbedding($text)
    // {
    //     $cacheKey = 'embedding_' . md5($text);

    //     // Check if the embedding exists in cache
    //     if (Cache::has($cacheKey)) {
    //         return Cache::get($cacheKey);
    //     }

    //     // Make the request to Nomic API
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . config('services.nomic.api_key'),
    //         'Content-Type'  => 'application/json',
    //     ])->post('https://api-atlas.nomic.ai/v1/embedding/text', [
    //         'model' => 'nomic-embed-text-v1',   // Change this to the appropriate model if necessary
    //         'texts' => [$text],  // Correct format for input
    //     ]);

    //     // Check if the request was successful
    //     if (!$response->successful()) {
    //         throw new \Exception('Nomic API error: ' . $response->body());
    //     }

    //     // Get the embedding vector from the response
    //     $embedding = $response->json()['embeddings'][0];

    //     // Format the embedding as PostgreSQL vector literal (without quotes)
    //     $formattedEmbedding = '[' . implode(',', $embedding) . ']';

    //     // Store the embedding in cache
    //     // Cache::put($cacheKey, $formattedEmbedding, now()->addDays(30));

    //     return $formattedEmbedding;
    // }

}
