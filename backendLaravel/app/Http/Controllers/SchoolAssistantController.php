<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Models\DocumentEmbedding;
use App\Services\DocumentProcessor;
use App\Services\NomicEmbeddingService;
use App\Services\GeminiEmbeddingService;
use App\Services\QueryAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\AiModelsProdviders\GeminiAiModelParams;
use App\Services\AiModelsProdviders\NomicAiModelParams;
class SchoolAssistantController extends Controller
{
    protected $documentProcessor;
    protected $queryAnswerService;
    protected $nomicEmbeddingService;
    protected $geminiEmbeddingService;
    
    public function __construct(
        DocumentProcessor $documentProcessor, 
        QueryAnswerService $queryService,
        NomicEmbeddingService $nomicEmbeddingService,
        GeminiEmbeddingService $geminiEmbeddingService
    
    )
    {
        $this->documentProcessor = $documentProcessor;
        $this->queryAnswerService = $queryService;
        $this->nomicEmbeddingService = $nomicEmbeddingService;
        $this->geminiEmbeddingService = $geminiEmbeddingService;
    }
    
    /**
     * Display the school assistant interface
     */
    public function index()
    {
        return view('school-assistant');
    }
    
    /**
     * Upload and process a new document after
     */
    public function uploadDocument(Request $request , $ModelAiProvider='gemini')
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
        
        // Process the document in the background but i don't what process the documnet by laravel
        // i want process the document by python FastAPI service
            ProcessDocumentJob::dispatch($document,$ModelAiProvider);

        return response()->json([
            'message' => 'Document uploaded and is being processed',
            'document' => $document
        ]);
    }
    

    /**
     * Ask a question to the assistant
     */
    public function askQuestion(QuestionRequest $request,$ModelAiProvider='gemini')
     
    {

           // get question
       $question =  $request->question;
       // make generate embedding for question
       // make generate answer for question
       // decide which model to use
        switch ($ModelAiProvider){
            case 'groq':
                // $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'cohere':
                // $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'openai':
                // $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'gemini':
                $questionEmbedding = $this->geminiEmbeddingService->generate_Embedding($question,TaskType:GeminiAiModelParams::TASK_TYPE_RETRIEVAL_QUERY);
                break;
            default:
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,TaskType:NomicAiModelParams::TASK_TYPE_SEARCH_QUERY);
                break;
        }

        // $answer = $this->nomicEmbeddingService->generate_Answer($question,$questionEmbedding);
        $embeddedSize = count($questionEmbedding["values"]);  

        if($questionEmbedding != null){
            return response()->json([
                'question' => $request->question,
                'embeddedSize' => $embeddedSize,
                'answer' => $questionEmbedding["values"]
            ]);
        }

        return response()->json([
            'question' => $request->question,
            'answer' => "the embedding not sucessfully generated"
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
