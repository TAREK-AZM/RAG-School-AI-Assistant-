<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\AiModelsProdviders\CohereAiModelParams;
use App\Services\AIServiceInterface;

class CohereEmbeddingService implements AIServiceInterface
{
    

    public function generate_Answer($question,$questionEmbedding,$ModelAiProvider='cohere'){}


    public function generate_Embedding(
        $Text,
        $EmbeddingModel = CohereAiModelParams::EMBEDDING_MODEL_MULTI_V3,
        $TaskType = CohereAiModelParams::TASK_TYPE_SEARCH_DOCUMENT,
        $Dimention = CohereAiModelParams::EMBED_DIM_V4_DEFAULT_768,
        $temperature = CohereAiModelParams::CHAT_TEMPERATURE_DEFAULT,
        $max_tokens = CohereAiModelParams::CHAT_MAX_TOKENS_DEFAULT,
        $EmbeddingType = CohereAiModelParams::EMBED_TYPE_FLOAT
        ){

            $cacheKey = "cohere_{$EmbeddingModel}_{$TaskType}_{$Dimention}_" . md5($Text);

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $payload = [
                'model'          => $EmbeddingModel,
                'input_type'     => $TaskType,
                'embedding_types'=> [$EmbeddingType],
                'texts'          => [$Text],
                // 'temperature'    => $temperature,
                // 'max_tokens'     => $max_tokens,
            ];

            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.cohere.api_key'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.cohere.com/v2/embed', $payload);

            if (!$res->successful()) {
                throw new \Exception('Cohere API error: ' . $res->body());
            }

            $embedding = $res->json()['embeddings']["float"]; // array of floats
            $formatted = '[' . implode(',',$embedding[0] ) . ']'; // PGvector literal

            Cache::put($cacheKey, $formatted, now()->addDays(30));

            return $formatted;
           
    }


    
    
        public function generate_Text($Text, $EmbeddingModel, $TaskType, $Dimention, $temperature, $max_tokens){}





}
