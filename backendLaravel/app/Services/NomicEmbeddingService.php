<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\aiModels\NomicAiModelParams;
USE App\Services\promts\AnswerPromts;

class NomicEmbeddingService implements AIServiceInterface
{
   

    public function generate_Answer($Text, $questionEmbedding)
    {
        $relevantDocs = DatabaseGetEmbedding::findRelevantDocuments($questionEmbedding);
        // Step 1: Build the context block from documents
        $context = "You have access to the following information from official school documents:\n\n";

        foreach ($relevantDocs as $doc) {
            // Debugging: show document title and content in terminal
            // echo "-------- DOCUMENT DEBUG --------\n";
            // echo "Title: {$doc->title}\n";
            // echo "Content:\n{$doc->content}\n\n";
            // echo "--------------------------------\n";

            // Append to context
            $context .= "Document: {$doc->content}\n";
        }

        // Step 2: Prepare the refined system prompt
        $systemPrompt = AnswerPromts::NOMIC_PROMT_ANSWER_QUESTION;

        // Step 3: Call the LLM API (Groq in this case)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => "Question: " . $Text ."\n\n "."Context: \n\n".  $context 
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 500
        ]);

        // Step 4: Handle errors gracefully
        if (!$response->successful()) {
            // Log::error("LLM API error: " . $response->body());
            return "I'm sorry, I encountered an error generating a response. Please try again later.\n\n" . $response->body();
        }

        // Step 5: Return the assistant’s answer
        return $response->json()['choices'][0]['message']['content'];
    }




    public function generate_Embedding( string $Text, string $eEmbeddingModel = NomicAiModelParams::EMBEDDING_MODEL_V1_5,string  $TaskType = NomicAiModelParams::TASK_TYPE_SEARCH_DOCUMENT,int $Dimention = NomicAiModelParams::EMBED_DIM_V1_5,float $temperature = NomicAiModelParams::TEXT_TEMPRETURE_V1_5,int  $max_tokens = NomicAiModelParams::MAX_TOKENS_V1_5)
    {
        // 1️⃣  Build a unique cache key that encodes embedding “flavor”
        $cacheKey = "embedding_{$TaskType}_{$Dimention}_" . md5($Text);

        // // 2️⃣  If we already have that vector in Laravel’s cache…
        if (Cache::has($cacheKey)) {

            // 3️⃣  …return it immediately and skip the Nomic API call.
            return Cache::get($cacheKey);
        }

        // Nomic API payload
        $payload = [
            'texts'     => [$Text],
            'model'     => 'nomic-embed-text-v1.5',
            'task_type' => $TaskType,           //  <-- critical switch
            'dim'       => $Dimention,               // 256/512 recommended
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.nomic.api_key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api-atlas.nomic.ai/v1/embedding/text', $payload);

        if (!$response->successful()) {
            throw new \Exception('Nomic API error: ' . $response->body());
        }

        $embedding = $response->json()['embeddings'][0];   // array of floats
        $formatted = '[' . implode(',', $embedding) . ']'; // PGvector literal

        Cache::put($cacheKey, $formatted, now()->addDays(30));

        return $formatted;
    }


    public function generate_Text($Text, $EmbeddingModel, $TaskType, $Dimention, $temperature, $max_tokens) {}





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
