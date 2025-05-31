<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Jobs\ProcessDocumentJob;
use App\Models\Document;
use App\Models\DocumentEmbedding;
use App\Services\DocumentProcessor;
use App\Services\NomicEmbeddingService;
use App\Services\GeminiEmbeddingService;
use App\Services\CohereEmbeddingService;
use App\Services\QueryAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Services\AiModelsProdviders\GeminiAiModelParams;
use App\Services\AiModelsProdviders\NomicAiModelParams;
use App\Services\AiModelsProdviders\CohereAiModelParams;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Console\Commands\SendMessage;

class SchoolAssistantController extends Controller
{
    protected $documentProcessor;
    protected $queryAnswerService;
    protected $nomicEmbeddingService;
    protected $geminiEmbeddingService;
    protected $cohereEmbeddingService;

    public function __construct(
        DocumentProcessor $documentProcessor,
        QueryAnswerService $queryService,
        NomicEmbeddingService $nomicEmbeddingService,
        GeminiEmbeddingService $geminiEmbeddingService,
        CohereEmbeddingService $cohereEmbeddingService

    ) {
        $this->documentProcessor = $documentProcessor;
        $this->queryAnswerService = $queryService;
        $this->nomicEmbeddingService = $nomicEmbeddingService;
        $this->geminiEmbeddingService = $geminiEmbeddingService;
        $this->cohereEmbeddingService = $cohereEmbeddingService;
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
    public function uploadDocument(Request $request, $ModelAiProvider = 'nomic')
    {

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'required|file|mimes:pdf,doc,docx,txt,rtf,ppt,pptx,xls,xlsx,csv,rar,zip,7z', //|max:10240
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $files = $request->file('documents'); // Multiple files
        // $fileContent = file_get_contents($file->getRealPath());
        // $path  =$file->store('documents');

        try {
            foreach ($files as $file) {
                $fileName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $filePath = $file->store('documents');
                // SendMessage::sendToRabbitMQ([
                //     'file' => $file,
                //     'file_name' => $fileName,
                //     'mime_type' => $mimeType,
                //     'provider' => $ModelAiProvider
                // ]);
            }
            // Create document record
            $document = Document::create([
                'title' => $request->title,
                'category' => $request->category ?? 'General',
                'filename' => basename($filePath),
                'filepath' => $filePath,
                // 'uploaded_by' => 123 ,// Auth::id()
            ]);

            ProcessDocumentJob::dispatch($document,$ModelAiProvider);

            return response()->json([
                'message' => 'Document uploaded and is being processed',
                'document' => $document
            ]);
        } catch (Exception $e) {
            $mimeType = 'application/octet-stream';
            $fileSize = 0;
        }






        // Send the file to rabbitmq



        // Process the document in the background but i don't what process the documnet by laravel
        // i want process the document by python FastAPI service


    }




    /**
     * Ask a question to the assistant
     */
    public function askQuestion(QuestionRequest $request, $ModelAiProvider = null)

    {

        // get question
        $question =  $request->question;
        // make generate embedding for question
        // make generate answer for question
        // decide which model to use
        switch ($ModelAiProvider) {
            case 'deepseek':
                // $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'groq':
                // $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'cohere':
                $questionEmbedding = $this->cohereEmbeddingService->generate_Embedding($question, TaskType: CohereAiModelParams::TASK_TYPE_SEARCH_QUERY);
                break;
            case 'openai':
                // $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question,'nomic');
                break;
            case 'gemini':
                $questionEmbedding = $this->geminiEmbeddingService->generate_Embedding($question, TaskType: GeminiAiModelParams::TASK_TYPE_RETRIEVAL_QUERY);
                break;
            default:
                $questionEmbedding = $this->nomicEmbeddingService->generate_Embedding($question, TaskType: NomicAiModelParams::TASK_TYPE_SEARCH_QUERY);
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

        // Calculate statistics for the dashboard
        $stats = [
            'total_documents' => Document::count(),
            'uploaded_today' => Document::whereDate('created_at', today())->count(),
            'categories' => Document::select('category')->distinct()->count(),
            'completed' => Document::where('status', 'completed')->count(),
            'failed' => Document::where('status', 'failed')->count()
        ];

        return view('admin-dashboard', compact('documents', 'stats'));
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
    public function deleteDocument(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        Storage::delete($document->filepath);
        $document->embeddings()->delete();
        $document->delete();

        return $this->listDocuments($request)
            ->with('success', 'Document and related embeddings deleted successfully');
    }

    public function getDocument($id)
    {
        $document = Document::findOrFail($id);
        return response()->json($document);
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
