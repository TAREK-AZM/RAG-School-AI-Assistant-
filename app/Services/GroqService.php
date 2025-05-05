<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService implements AiServiceInterface
{
    
    public function generate_Answer($Text, $EmbeddingModel){
    }
    public function generate_Embedding($Text, $EmbeddingModel, $TaskType, $Dimention,$temperature, $max_tokens){

    }
    public function generate_Text($Text, $EmbeddingModel, $TaskType, $Dimention,$temperature, $max_tokens){

    }



}