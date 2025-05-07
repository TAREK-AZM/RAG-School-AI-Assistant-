<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService implements AiServiceInterface
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
        $systemPrompt = <<<'EOT'
    You are a helpful academic assistant for students interested in engineering schools.
    Only answer using the content provided in the documents.
    If the answer cannot be found in the context, say: "I don't have enough information to answer that."
    Avoid phrases like "Based on the provided context" or "From the documents" — just answer clearly and naturally as if speaking to a student.
    EOT;

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
                    'content' => $context . "\n\nQuestion: " . $Text
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




    public function generate_Embedding($Text, $EmbeddingModel, $TaskType, $Dimention,$temperature, $max_tokens){
      // Groq API not suport embedding untill this moment
    }


    public function generate_Text($Text, $EmbeddingModel, $TaskType, $Dimention,$temperature, $max_tokens){
        
    }



}