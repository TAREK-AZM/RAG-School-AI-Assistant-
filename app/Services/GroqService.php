<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    public function chatCompletion(array $messages, float $temperature = 0.3)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model'),
                'messages' => $messages,
                'temperature' => $temperature,
            ]);

            if (!$response->successful()) {
                Log::error('Groq API error: ' . $response->body());
                throw new \Exception('Failed to get response from Groq API');
            }

            return $response->json()['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            Log::error('Groq service error: ' . $e->getMessage());
            throw $e;
        }
    }
}