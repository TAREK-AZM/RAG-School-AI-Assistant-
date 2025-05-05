<?php

namespace App\Services;
use App\Services\aiModels\NomicAiModelParams;
class GenerateEmbedding

{

    // protected $groqService;
    // protected $cohereService;
    protected $nomicService;

    public function __construct(GroqService $groqService,CohereEmbeddingService $cohereService,NomicEmbeddingService $nomicService)
    {
        // $this->groqService = $groqService;
        // $this->cohereService = $cohereService;
        $this->nomicService = $nomicService;
    }
    public function generateEmbedding($Text,$ModelProvider, $eEmbeddingModel = NomicAiModelParams::EMBEDDING_MODEL_V1_5 , $TaskType = NomicAiModelParams::TASK_TYPE_SEARCH_DOCUMENT , $Dimention = NomicAiModelParams::EMBED_DIM_V1_5 , $temperature =NomicAiModelParams::TEXT_MODEL_V1_5 , $max_tokens =NomicAiModelParams::MAX_TOKENS_V1_5 ){


        switch ($ModelProvider){
            case 'groq':
                // return $this->groqService->generate_Embedding($Input,$EmbeddingModelName);
            case 'cohere':
                // return $this->cohereService->generate_Embedding($Input,$EmbeddingModelName);
            case 'openai':
                // return $this->openaiService->generate_Embedding($Input,$EmbeddingModelName);
            case 'nomic':
                return $this->nomicService->generate_Embedding($Text, $eEmbeddingModel , $TaskType );
        }
    }
}