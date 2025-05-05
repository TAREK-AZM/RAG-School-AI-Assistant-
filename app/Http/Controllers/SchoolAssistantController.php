<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Models\DocumentEmbedding;
use App\Services\DocumentProcessor;
use App\Services\GenerateEmbedding;
use App\Services\NomicEmbeddingService;
use App\Services\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\aiModels\NomicAiModelParams;
class SchoolAssistantController extends Controller
{
    protected $documentProcessor;
    protected $queryService;
    protected $nomicEmbeddingService;
    
    public function __construct(DocumentProcessor $documentProcessor, QueryService $queryService,NomicEmbeddingService $nomicEmbeddingService)
    {
        $this->documentProcessor = $documentProcessor;
        $this->queryService = $queryService;
        $this->nomicEmbeddingService = $nomicEmbeddingService;
    }
    
    /**
     * Display the school assistant interface
     */
    public function index()
    {
        return view('school-assistant');
    }
    
    /**
     * Upload and process a new document
     */
    public function uploadDocument(Request $request , $ModelAiProvider='nomic')
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);
        
        $path = $request->file('document')->store('documents');
        
        // Create document record
        $document = Document::create([
            'title' => $request->title,
            'category' => $request->category ?? 'General',
            'filename' => basename($path),
            'filepath' => $path,
            // 'uploaded_by' => 123 ,// Auth::id()
        ]);
        
        // Process the document in the background
        ProcessDocumentJob::dispatch($document,$ModelAiProvider);
        
        return response()->json([
            'message' => 'Document uploaded and is being processed',
            'document' => $document
        ]);
    }
    
    /**
     * Ask a question to the assistant
     */
    public function askQuestion(QuestionRequest $request,$ModelAiProvider='nomic')
     
    {

           // get question
       $question =  $request->question;
       // make generate embedding for question
       // make generate answer for question
       // decide which model to use
        switch ($ModelAiProvider){
            case 'groq':
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'cohere':
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'openai':
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'nomic':
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,null,NomicAiModelParams::TASK_TYPE_SEARCH_QUERY);

            default:
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,NomicAiModelParams::EMBEDDING_TEMPRETURE_V1_5,NomicAiModelParams::TASK_TYPE_SEARCH_QUERY,NomicAiModelParams::EMBED_DIM_V1_5,NomicAiModelParams::TEXT_TEMPRETURE_V1_5,NomicAiModelParams::MAX_TOKENS_V1_5);
                break;
        }

        $answer = $this->nomicEmbeddingService->generate_Answer($question,$questionEmbedding);
        
        return response()->json([
            'question' => $request->question,
            'answer' => $answer
        ]);
    }
    
    /**
     * List all documents
     */
    public function listDocuments(Request $request)
    {
        // $query = Document::all();
        
        // if ($request->has('category')) {
        //     $query->where('category', $request->category);
        // }
        
        $documents = Document::all();
        
        return response()->json($documents);
    }

    public function listChunks(Request $request)
    {
        // $query = Document::all();
        
        // if ($request->has('category')) {
        //     $query->where('category', $request->category);
        // }
        
        $documents = DocumentEmbedding::all();
        
        return response()->json($documents);
    }
    
    /**
     * Delete a document and its embeddings
     */
    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        
        // Delete the file
        Storage::delete($document->filepath);
        
        // Delete related embeddings and the document
        $document->embeddings()->delete();
        $document->delete();
        
        return response()->json([
            'message' => 'Document and related embeddings deleted successfully'
        ]);
    }
    public function deleteDocumentAll()
    {
        $documents = Document::all();
        
        // Delete the file
        foreach ($documents as $document) {
            $document->embeddings()->delete();
            Storage::delete($document->filepath);
            $document->delete();
        }
                
        return response()->json([
            'message' => 'Document and related embeddings deleted successfully'
        ]);
    }
}
