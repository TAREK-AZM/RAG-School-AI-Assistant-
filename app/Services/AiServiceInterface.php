<?php

namespace App\Services;

interface AIServiceInterface
{
    public function  generate_Answer($Text,$RelevantDocuments);
    public function generate_Embedding($Text, $EmbeddingModel, $TaskType, $Dimention,$temperature, $max_tokens);
    public function generate_Text($Text, $EmbeddingModel, $TaskType, $Dimention,$temperature, $max_tokens);


}