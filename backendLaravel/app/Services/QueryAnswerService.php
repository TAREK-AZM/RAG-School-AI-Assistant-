<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentEmbedding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\NomicEmbeddingService;
class QueryAnswerService
{
    protected $nomicEmbeddingService;
    protected $geminiEmbeddingService;
    
    public function __construct(NomicEmbeddingService $embeddingService, GeminiEmbeddingService $geminiEmbeddingService)
    {
        $this->nomicEmbeddingService = $embeddingService;
        $this->geminiEmbeddingService = $geminiEmbeddingService;
    }
    
    /**
     * Answer a question based on relevant documents
     *
     * @param string $question
     * @param string|null $category
     * @return string
     */

    public function answerQuestion($question,$embedding,$ModelAiProvider)
    {
        try {
            
            // Find relevant documents
            $relevantDocs = DataBaseGetEmbedding::findRelevantDocuments($embedding);
            
            if (empty($relevantDocs)) {
                return "I don't have information about that in my knowledge base. Please ask something related to the school documents that have been uploaded.";
            }
            
            
            // Generate answer using LLM
            // here depend of the model
            switch ($ModelAiProvider){
                case 'groq':
                    return $this->nomicEmbeddingService->generate_Answer($question, $relevantDocs);
                case 'cohere':
                    return $this->nomicEmbeddingService->generate_Answer($question, $relevantDocs);
                case 'gemini':
                    return $this->geminiEmbeddingService->generate_Answer($question, $relevantDocs);
                default:// nomic
                    return $this->nomicEmbeddingService->generate_Answer($question, $relevantDocs);
            }
            // ============ Testing ============
            // $contents = [];
            // foreach ($relevantDocs as $doc) {
            //     $contents[] = $doc->content;
            // }
            // return implode("\n\n", $contents);
            // return $relevantDocs;
            // ============ Testing ============
        } catch (\Exception $e) {
            Log::error("Error answering question: " . $e->getMessage());
            return "I'm sorry, I encountered an error while processing your question. Please try again later.";
        }
    }
    
  
}
