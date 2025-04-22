<?php
namespace App\Jobs;
use App\Models\Document;
use App\Services\DocumentProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $document;
    public $timeout = 3600; // 1 hour max processing time

    /**
     * Create a new job instance.
     *
     * @param Document $document
     * @return void
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     *
     * @param DocumentProcessor $documentProcessor
     * @return void
     */
    public function handle(DocumentProcessor $documentProcessor)
    {
        try {
            $this->document->update(['status' => 'processing']);
            
            $chunkCount = $documentProcessor->processDocument($this->document);
            
            $this->document->update([
                'status' => 'completed',
                'chunk_count' => $chunkCount,
                'processed_at' => now()
            ]);
            
            Log::info("Document {$this->document->id} processed successfully with {$chunkCount} chunks");
        } catch (\Exception $e) {
            $this->document->update(['status' => 'failed']);
            Log::error("Failed to process document {$this->document->id}: " . $e->getMessage());
            throw $e;
        }
    }
}