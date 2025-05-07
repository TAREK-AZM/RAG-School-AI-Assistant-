<?php

namespace App\Services;

interface AIServiceInterface
{
    public function  generate_Answer(string  $Text,string $RelevantDocuments);
    public function generate_Embedding(string $Text,string $EmbeddingModel, string $TaskType, int $Dimention,float $temperature, int $max_tokens);
    public function generate_Text(string $Text,string $EmbeddingModel, string $TaskType, int $Dimention,float $temperature, int $max_tokens);


}