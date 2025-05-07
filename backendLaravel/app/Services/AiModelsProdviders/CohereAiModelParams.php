<?php
namespace App\Services\AiModelsProdviders;

/**
 * Canonical list of Cohere‑hosted model names, default hyper‑parameters
 * and API‑specific enums, so the rest of your code can reference constants
 * instead of hard‑coding strings.
 */
class CohereAiModelParams
{
    /* ---------- Chat / Generative models ---------- */
    public const CHAT_MODEL_COMMAND_R       = 'command-r';
    public const CHAT_MODEL_COMMAND_R_PLUS  = 'command-r-plus';

    /* ---------- Embedding models (v3 & v4) ---------- */
    public const EMBEDDING_MODEL_EN_V3          = 'embed-english-v3.0';
    public const EMBEDDING_MODEL_MULTI_V3       = 'embed-multilingual-v3.0';
    public const EMBEDDING_MODEL_V4             = 'embed-v4.0';        // supports output_dimension

    /* ---------- Default generation hyper‑params ---------- */
    public const CHAT_TEMPERATURE_DEFAULT   = 0.3;
    public const CHAT_TOP_P_DEFAULT         = 0.95;
    public const CHAT_MAX_TOKENS_DEFAULT    = 512;

    /* ---------- Embedding defaults ---------- */
    // v3 models always return 1024‑D vectors.
    public const EMBED_DIM_V3               = 1024;

    // v4 allows 256 | 512 | 1024 | 1536  – pick one:
    public const EMBED_DIM_V4_DEFAULT       = 768;

    /* ---------- Cohere input‑type enum ---------- */
    public const TASK_TYPE_SEARCH_DOCUMENT = 'search_document';
    public const TASK_TYPE_SEARCH_QUERY    = 'search_query';
    public const TASK_TYPE_CLASSIFICATION  = 'classification';
    public const TASK_TYPE_CLUSTERING      = 'clustering';
    public const TASK_TYPE_IMAGE           = 'image';

    /* ---------- embedding_types enum ---------- */
    public const EMBED_TYPE_FLOAT           = 'float';
    public const EMBED_TYPE_INT8            = 'int8';
    public const EMBED_TYPE_UINT8           = 'uint8';
    public const EMBED_TYPE_BINARY          = 'binary';
    public const EMBED_TYPE_UBINARY         = 'ubinary';
}
