<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentEmbedding;
use Illuminate\Database\Seeder;

class DocumentEmbeddingSeeder extends Seeder
{
    public function run()
    {
        // Get the first document
        $document = Document::first();
        
        if (!$document) {
            $this->command->info('No documents found. Please run DocumentSeeder first.');
            return;
        }

        // Sample embeddings
        $embeddings = [
            [
                'content' => 'The school has a strict anti-bullying policy...',
                'chunk_index' => 0,
            ],
            [
                'content' => 'Attendance is mandatory for all students...',
                'chunk_index' => 1,
            ],
        ];

        foreach ($embeddings as $embedding) {
            DocumentEmbedding::create([
                'document_id' => $document->id,
                'content' => $embedding['content'],
                'chunk_index' => $embedding['chunk_index'],
                'embedding' => $this->generateProperVector(1536), // Updated method
            ]);
        }
    }

    /**
     * Generate a properly formatted pgvector array
     */
    private function generateProperVector(int $dimensions): string
    {
        $values = array_map(fn() => mt_rand(0, 100) / 100, array_fill(0, $dimensions, null));
        
        // Return as PostgreSQL vector literal WITHOUT quotes
        return '[' . implode(',', $values) . ']';
    }
}