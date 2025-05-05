<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CohereEmbeddingService
{
    /**
     * Generate an embedding (float, int8, etc.) via Cohere v2 API.
     *
     * @param  string $text          The text (or image data URI) to embed.
     * @param  string $role          'search_document' | 'search_query' | 'classification' | 'clustering'
     * @param  int    $dim           256 | 512 | 1024 | 1536   (v4+ models only)
     * @param  array  $types         ['float'] or ['float','int8'] … up to five
     * @param  string $model         Default: embed-multilingual-v3.0
     * @return array                 ['float' => '[…, …]', 'int8' => '[…]', …]
     * @throws \Exception
     */
    public function embed(
        string $text,
        string $role   = 'search_document',
        int    $dim    = 768,
        array  $types  = ['float'],
        string $model  = 'embed-multilingual-v3.0'
    ): array {
        // ---------- build cache key ----------
        $cacheKey = "cohere_{$model}_{$role}_{$dim}_" . md5($text);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // ---------- request payload ----------
        $payload = [
            'model'          => $model,
            'input_type'     => $role,
            'embedding_types'=> $types,
            'texts'          => [$text],
        ];

        // output_dimension is only valid for v4+ models
        if ($dim !== 1536 && str_contains($model, 'embed-v4')) {
            $payload['output_dimension'] = $dim;
        }

        $res = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.cohere.api_key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.cohere.com/v2/embed', $payload);

        if (!$res->successful()) {
            throw new \Exception('Cohere API error: '.$res->body());
        }

        // ---------- format for pgvector ----------
        $data = $res->json()['embeddings']; // { float: [[…]], int8: [[…]], … }

        $out = [];
        foreach ($data as $t => $vecs) {
            $out[$t] = '['.implode(',', $vecs[0]).']';   // first (and only) item
        }

        Cache::put($cacheKey, $out, now()->addDays(30));
        return $out;
    }



    // $svc = app()->make(\App\Services\CohereEmbeddingService::class);

    // // chunk embedding
    // $docVecs = $svc->embed($chunkText, 'search_document', 768, ['float']);
    
    // // query embedding
    // $qVecs   = $svc->embed($userQuestion, 'search_query', 768, ['float']);
    // $qFloat  = $qVecs['float'];          // store / search with this literal
    






}
