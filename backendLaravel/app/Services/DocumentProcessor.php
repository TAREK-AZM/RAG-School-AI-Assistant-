<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentEmbedding;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\DB;
use Laravel\Embeddings\Splitters\Re;

class DocumentProcessor
{
    protected $nomicEmbeddingService;
    protected $geminiEmbeddingService;

    public function __construct(NomicEmbeddingService $embeddingService, GeminiEmbeddingService $geminiEmbeddingService)
    {
        $this->nomicEmbeddingService = $embeddingService;
        $this->geminiEmbeddingService = $geminiEmbeddingService;
    }

    /**
     * Process a document and create embeddings
     *
     * @param Document $document
     * @return int Number of chunks processed
     */
    public function processDocument($ModelAiProvider,Document $document)
    {
        // Extract text from the document
        $text = $this->extractText($document->filepath);
        
        // Clean and chunk the text
        $chunks = $this->chunkText($text);
        
        // Generate and store embeddings
        $this->processChunks($ModelAiProvider,$document, $chunks);
        
        return count($chunks);
    }
    
    /**
     * Extract text from a document file
     *
     * @param string $filePath
     * @return string
     */
    private function extractText($filePath)
    {
        $extension = pathinfo(Storage::path($filePath), PATHINFO_EXTENSION);
        
        switch(strtolower($extension)) {
            case 'pdf':
                return (new Pdf())
                    ->setPdf(Storage::path($filePath))
                    ->text();
            case 'txt':
                return Storage::get($filePath);
            case 'doc':
            case 'docx':
                // This would need a Word document parser
                // For example, you could use PhpOffice/PhpWord
                // For now, we'll just return an error message
                throw new \Exception("Document type not yet supported: $extension");
            default:
                throw new \Exception("Unsupported file type: $extension");
        }
    }
    
    /**
     * Clean and chunk text into manageable pieces
     *
     * @param string $text
     * @return array
     */
    private function chunkText($text)
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // Split by paragraphs
        $paragraphs = preg_split('/\n\s*\n|\r\n\s*\r\n/', $text);
        
        $chunks = [];
        $currentChunk = '';
        $chunkSize = config('vectordb.chunk_size', 500);
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) continue;
            
            // If adding this paragraph would exceed the chunk size, save the current chunk
            if (strlen($currentChunk) + strlen($paragraph) > $chunkSize && !empty($currentChunk)) {
                $chunks[] = trim($currentChunk);
                $currentChunk = '';
            }
            
            // If the paragraph itself is larger than chunk size, split it by sentences
            if (strlen($paragraph) > $chunkSize) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $paragraph);
                
                foreach ($sentences as $sentence) {
                    if (strlen($currentChunk) + strlen($sentence) > $chunkSize && !empty($currentChunk)) {
                        $chunks[] = trim($currentChunk);
                        $currentChunk = '';
                    }
                    
                    $currentChunk .= $sentence . ' ';
                }
            } else {
                $currentChunk .= $paragraph . "\n\n";
            }
        }
        
        // Add the final chunk if not empty
        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }
        
        return $chunks;
    }
    


    private function processChunks($ModelAiProvider,Document $document, array $chunks)
    {
        foreach ($chunks as $index => $chunk) {
            // Generate embedding for the chunk
            $embedding=null;
            // here depend of the model
            switch ($ModelAiProvider){
                case 'groq':
                    // return $this->generateAnswer($question, $relevantDocs);
                    break;
                case 'cohere':
                    // return $this->generateAnswer($question, $relevantDocs);
                    break;
                case 'gemini':
                    return $this->geminiEmbeddingService->generate_Embedding($chunk);
                    break;
                default: // nomic
                    $embedding = $this->nomicEmbeddingService->generate_Embedding($chunk);
                    break;
            }
            

            // Store the chunk and its embedding
            DocumentEmbedding::create([
                'document_id' => $document->id,
                'content' => $chunk,
                'embedding' => DB::raw("'{$embedding}'::vector"), // Use DB::raw() for vector literal
                'chunk_index' => $index,
            ]);
        }
    }
    
    
}

