<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Services\DocumentProcessor;
use App\Services\QueryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SchoolAssistantController extends Controller
{
    protected $documentProcessor;
    protected $queryService;
    
    public function __construct(DocumentProcessor $documentProcessor, QueryService $queryService)
    {
        $this->documentProcessor = $documentProcessor;
        $this->queryService = $queryService;
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
    public function uploadDocument(Request $request)
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
            'uploaded_by' => 123 ,// Auth::id()
        ]);
        
        // Process the document in the background
        ProcessDocumentJob::dispatch($document);
        
        return response()->json([
            'message' => 'Document uploaded and is being processed',
            'document' => $document
        ]);
    }
    
    /**
     * Ask a question to the assistant
     */
    public function askQuestion(QuestionRequest $request)
    {
        $answer = $this->queryService->answerQuestion(
            $request->question,
            $request->category ?? null
        );
        
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
        $query = Document::query();
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $documents = $query->paginate(15);
        
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
}
