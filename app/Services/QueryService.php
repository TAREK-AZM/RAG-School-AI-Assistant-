<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentEmbedding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QueryService
{
    protected $embeddingService;
    
    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }
    
    /**
     * Answer a question based on relevant documents
     *
     * @param string $question
     * @param string|null $category
     * @return string
     */
    public function answerQuestion($question, $category = null)
    {
        // try {
            // Generate embedding for the question
            $embedding = $this->embeddingService->generateEmbedding($question);
            
            // Find relevant documents
            $relevantDocs = $this->findRelevantDocuments($embedding, $category);
            
            if (empty($relevantDocs)) {
                return "I don't have information about that in my knowledge base. Please ask something related to the school documents that have been uploaded.";
            }
            
            
            // Generate answer using LLM
            return $this->generateAnswer($question, $relevantDocs);
            // ============ Testing ============
            // $contents = [];
            // foreach ($relevantDocs as $doc) {
            //     $contents[] = $doc->content;
            // }
            // return implode("\n\n", $contents);
            // return $relevantDocs;
            // ============ Testing ============
        // } catch (\Exception $e) {
        //     Log::error("Error answering question: " . $e->getMessage());
        //     return "I'm sorry, I encountered an error while processing your question. Please try again later.";
        // }
    }
    
    /**
     * Find relevant documents based on embedding similarity
     *
     * @param string $embedding JSON encoded embedding vector
     * @param string|null $category
     * @return array
     */
    private function findRelevantDocuments($embedding, $category = null)
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
        
        if ($category) {
            $query->where('documents.category', $category);
        }
        
        $results = $query->orderByRaw("embedding <=> '$embedding'::vector")
            ->limit($limit)
            ->get();
            
        // Filter out low similarity results
        return $results->filter(function ($item) use ($threshold) {
            return $item->similarity >= $threshold;
        })->all();
    }
    
    /**
     * Generate an answer using an LLM
     *
     * @param string $question
     * @param array $relevantDocs
     * @return string
     */
//     private function generateAnswer($question, $relevantDocs)
//     {
//         // Prepare context from relevant documents
//         $context = "Based on the following information from school documents:\n\n";
        
//         foreach ($relevantDocs as $doc) {
//             $context .= "From \"{$doc->title}\" ({$doc->category}):\n{$doc->content}\n\n";
//         }
        
//         // Call the LLM API (OpenAI in this example)
//         $response = Http::withHeaders([
//             'Authorization' => 'Bearer ' . config('services.openai.api_key'),
//             'Content-Type' => 'application/json',
//         ])->post('https://api.openai.com/v1/chat/completions', [
//             'model' => config('services.openai.chat_model', 'gpt-3.5-turbo'),
//             'messages' => [
//                 [
//                     'role' => 'system',
//                     'content' => 'You are a helpful assistant for our school. Answer questions based only on the provided context. If the information isn\'t in the context, simply say you don\'t have that information in your knowledge base.'
//                 ],
//                 [
//                     'role' => 'user',
//                     'content' => $context . "\n\nQuestion: " . $question
//                 ]
//             ],
//             'temperature' => 0.3,
//             'max_tokens' => 500
//         ]);
        
//         if (!$response->successful()) {
//             Log::error("LLM API error: " . $response->body());
//             return "I'm sorry, I encountered an error generating a response. Please try again later.";
//         }
        
//         return $response->json()['choices'][0]['message']['content'];
//     }







private function generateAnswer($question, $relevantDocs)
{
    // Step 1: Build the context block from documents
    $context = "You have access to the following information from official school documents:\n\n";

    // foreach ($relevantDocs as $doc) {
    //     // Debugging: show document title and content in terminal
    //     echo "-------- DOCUMENT DEBUG --------\n";
    //     echo "Title: {$doc->title}\n";
    //     echo "Content:\n{$doc->content}\n\n";
    //     echo "--------------------------------\n";

    //     // Append to context
    //     $context .= "Document: {$doc->content}\n";
    // }

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
                'content' => $context . "\n\nQuestion: " . $question
            ]
        ],
        'temperature' => 0.3,
        'max_tokens' => 500
    ]);

    // Step 4: Handle errors gracefully
    if (!$response->successful()) {
        Log::error("LLM API error: " . $response->body());
        return "I'm sorry, I encountered an error generating a response. Please try again later.\n\n" . $response->body();
    }

    // Step 5: Return the assistant’s answer
    return $response->json()['choices'][0]['message']['content'];
}













// private function generateAnswer($question, $relevantDocs)
// {
//     // Prepare context from relevant documents
//     $context = "Based on the following information from school documents:\n\n";
//     // Debugging output before sending request
//     foreach ($relevantDocs as $doc) {
//         // here i want debug he i want show the content in terminal

//         $context .= "From \"{$doc->title}\:\n{$doc->content}\n\n";
//     }
    
//     // Call the Groq API
//     // $response = Http::withHeaders([
//     //     'Authorization' => 'Bearer ' . config('services.groq.api_key'),
//     //     'Content-Type' => 'application/json',
//     // ])->post('https://api.groq.com/v1/chat/completions ', [
//     //     'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
//     //     'messages' => [
//     //         [
//     //             'role' => 'system',
//     //             'content' => 'You are a helpful assistant for our school. Answer questions based only on the provided context. If the information isn\'t in the context, simply say you don\'t have that information in your knowledge base.'
//     //         ],
//     //         [
//     //             'role' => 'user',
//     //             'content' => $context . "\n\nQuestion: " . $question
//     //         ]
//     //     ],
//     //     'temperature' => 0.3,
//     //     'max_tokens' => 500
//     // ]);
//     $response = Http::withHeaders([
//         'Authorization' => 'Bearer ' . config('services.groq.api_key'),
//         'Content-Type' => 'application/json',
//     ])->post('https://api.groq.com/openai/v1/chat/completions', [
//         'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
//         'messages' => [
//             [
//                 'role' => 'system',
//                 'content' => 'You are a helpful assistant for our school. Answer questions based only on the provided context. If the information isn’t in the context, simply say you don’t have that information in your knowledge base.'
//             ],
//             [
//                 'role' => 'user',
//                 'content' => $context . "\n\nQuestion: " . $question
//             ]
//         ],
//         'temperature' => 0.3,
//         'max_tokens' => 500
//     ]);
    
    
//     if (!$response->successful()) {
//         Log::error("LLM API error: " . $response->body());
//         return "I'm sorry, I encountered an error generating a response. Please try again later.".$response->body() ;
//     }
    
//     return $response->json()['choices'][0]['message']['content'];
// }
}
