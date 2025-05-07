<?php
namespace App\Services\AiModelsProdviders;

/**
 * Canonical constants for **Google Gemini** chat‑ and embedding‑models plus the
 * task‑type enums that the `embed_content()` endpoint expects.
 *
 * Keep all magic strings here so you can swap versions in one place.
 */
class GeminiAiModelParams
{
    /* ─────────────── Chat / generation models ─────────────── */
    // (The public chat models are still named “gemini‑pro” & “gemini‑flash”;
    // only embeddings use the “gemini‑embedding‑…” naming scheme.)
    public const   CHAT_MODEL_PRO                  = 'gemini-pro';
    public const CHAT_MODEL_FLASH                = 'gemini-pro-vision';  // multimodal

    /* ─────────────── Embedding models ─────────────── */
    public const EMBEDDING_MODEL_EXP_03_07           = 'gemini-embedding-exp-03-07';
    public const EMBEDDING_MODEL_TEXT_EMB_004        = 'text-embedding-004';
    public const EMBEDDING_MODEL_V1                  = 'embedding-001';

    /* ─────────────── Task‑type enum for embeddings ─────────────── */
    // Optimises the embedding for the downstream job.
    public const TASK_TYPE_SEMANTIC_SIMILARITY        = 'SEMANTIC_SIMILARITY';
    public const TASK_TYPE_CLASSIFICATION             = 'CLASSIFICATION';
    public const TASK_TYPE_CLUSTERING                 = 'CLUSTERING';
    // Retrieval‑augmented options
    public const TASK_TYPE_RETRIEVAL_DOCUMENT         = 'RETRIEVAL_DOCUMENT';
    public const TASK_TYPE_RETRIEVAL_QUERY            = 'RETRIEVAL_QUERY';
    public const TASK_TYPE_QUESTION_ANSWERING         = 'QUESTION_ANSWERING';
    public const TASK_TYPE__VERIFICATION              = 'FACT_VERIFICATION';
    // Code‑specific
    public const TASK_TYPE_CODE_RETRIEVAL_QUERY       = 'CODE_RETRIEVAL_QUERY';

    /* ─────────────── Suggested defaults ─────────────── */
    // Chat (generation) hyper‑parameters
    public const TEXT_TEMPERATURE_DEFAULT        = 0.3;
    public const TEXT_MAX_TOKENS_DEFAULT         = 512;

    // The experimental embedding model returns 768‑D floats.
    public const EMBED_DIM_EXP_03_07_768             = 768;

    public const MAX_TOKENS_V_500 = 500;
    public const MAX_TOKENS_V_300 = 300;
    public const MAX_TOKENS_V_100 = 100;
}
