<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vector Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure settings for the RAG vector database
    |
    */
    
    // Size of text chunks for embeddings (in characters)
    'chunk_size' => env('VECTORDB_CHUNK_SIZE', 1000),
    
    // Number of top results to return from the vector search
    'top_k' => env('VECTORDB_TOP_K', 5),
    
    // Similarity threshold (0.0 - 1.0) for filtering results
    'similarity_threshold' => env('VECTORDB_SIMILARITY_THRESHOLD', 0.7),
];