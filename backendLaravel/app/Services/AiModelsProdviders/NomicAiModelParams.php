<?php
namespace App\Services\AiModelsProdviders;

class NomicAiModelParams{

    // models
    public const TEXT_MODEL_V1 = 'nomic-embed-text-v1';
    public const TEXT_MODEL_V1_5 = 'nomic-embed-text-v1.5';
    // embeddings models
    public const EMBEDDING_MODEL_V1 = 'nomic-embed-text-v1';
    public const EMBEDDING_MODEL_V1_5 = 'nomic-embed-text-v1.5';

    // parameters
    public const TEXT_TEMPRETURE_V1= 0.3;
    public const TEXT_TEMPRETURE_V1_5= 0.3;  
    // embeddings tempretures
    public const EMBEDDING_TEMPRETURE_V1= 0.3;
    public const EMBEDDING_TEMPRETURE_V1_5= 0.3;

    public const MAX_TOKENS_V1 = 500;
    public const MAX_TOKENS_V1_5 = 300;


    // task types
    public const TASK_TYPE_SEARCH_DOCUMENT = 'search_document';
    public const TASK_TYPE_SEARCH_QUERY = 'search_query';
    public const TASK_TYPE_IMAGE = 'nomic-embed-vision-v1.5';


     //  embedding dimensions
     public const EMBED_DIM_V1               = 768;
     public const EMBED_DIM_V1_5               = 768;
    
}