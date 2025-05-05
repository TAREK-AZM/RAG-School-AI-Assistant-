<?php
namespace App\Services\aiModels;

class GroqAiModelParams{

    /* ---------- Chat / completion models ---------- */
    public const LLAMA3_8B   = 'llama3-8b-8192';
    public const LLAMA3_70B  = 'llama3-70b-8192';          // flagship
    public const GROQ_MODEL='llama-3.3-70b-versatile';

    /* ---------- Embedding models (Groq proxies) ---------- */
    // You usually hit Nomic/Cohere/OpenAI endpoints directly for embeds,
    // but if you ever proxy through Groq’s /embeddings route:
    public const EMBED_BGE_BASE   = 'bge-base-en-v1.5';
    public const EMBED_NOMIC_V15  = 'nomic-embed-text-v1.5';

    /* ---------- Default generation parameters ---------- */
    public const TEMP_DEFAULT         = 0.3;   // balanced factual tone
    public const TOP_P_DEFAULT        = 0.95;
    public const PRESENCE_PENALTY     = 0.0;
    public const FREQUENCY_PENALTY    = 0.0;

    // Groq supports up to 8 k context on LLaMA‑3; set lower for cost control
    public const MAX_TOKENS_DEFAULT   = 512;

    /* ---------- RAG helper task‑type strings ---------- */
    public const TASK_SEARCH_DOCUMENT = 'search_document';
    public const TASK_SEARCH_QUERY    = 'search_query';
    
}