<?php

use App\Http\Controllers\SchoolAssistantController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Public routes
Route::get('/school-assistant', [SchoolAssistantController::class, 'index'])->name('school-assistant');

// Auth routes
// Route::middleware(['auth'])->group(function () {
    // Ask questions
    Route::post('/school-assistant/ask', [SchoolAssistantController::class, 'askQuestion'])->name('school-assistant.ask');
    
    // View documents
    Route::get('/school-assistant/documents', [SchoolAssistantController::class, 'listDocuments'])->name('school-assistant.documents');
// });

// Admin routes
// Route::middleware(['auth', 'admin'])->group(function () {
    // Upload document
    Route::post('/school-assistant/upload', [SchoolAssistantController::class, 'uploadDocument'])->name('school-assistant.upload');
    
    // Delete document
    Route::delete('/school-assistant/documents/{id}', [SchoolAssistantController::class, 'deleteDocument'])->name('school-assistant.delete');
// });

// Simple test route
Route::get('/test', function () {
    try {
        // Test database connection
        DB::connection()->getPdo();
        $dbStatus = 'Database connected: ' . DB::connection()->getDatabaseName();
        
        // Test pgvector extension
        $vectorTest = DB::select("SELECT '[1,2,3]'::vector <-> '[4,5,6]'::vector AS distance")[0]->distance;
        
        return response()->json([
            'status' => 'Hello World!',
            'database' => $dbStatus,
            'pgvector_test' => $vectorTest,
            'environment' => app()->environment(),
            'routes' => [
                'school-assistant' => route('school-assistant'),
                'ask-question' => route('school-assistant.ask'),
                'list-documents' => route('school-assistant.documents')
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        $vectorTest = DB::select("SELECT '[1,2,3]'::vector <-> '[4,5,6]'::vector AS distance")[0]->distance;
        
        return response()->json([
            'status' => 'Connected successfully',
            'database' => DB::connection()->getDatabaseName(),
            'pgvector_test' => $vectorTest
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});



Route::get('/test-embeddings', function() {
    // Ensure document exists
    if (!DB::table('documents')->where('id', 1)->exists()) {
        DB::table('documents')->insert([
            'title' => 'Test Document',
            'category' => 'Test',
            'filename' => 'test.pdf',
            'filepath' => 'test/test.pdf',
            'status' => 'completed',
            'uploaded_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // Clear previous test data
    DB::table('document_embeddings')->truncate();

    // Create test embeddings
    $identicalEmbedding = '[' . implode(',', array_fill(0, 1536, 0.42)) . ']';
    $similarEmbedding = '[' . implode(',', array_fill(0, 1536, 0.45)) . ']';
    $differentEmbedding = '[' . implode(',', array_fill(0, 1536, 0.9)) . ']';

    // Insert test data with all required fields
    DB::table('document_embeddings')->insert([
        [
            'document_id' => 1, 
            'content' => 'Identical content', 
            'chunk_index' => 0,
            'embedding' => DB::raw("'{$identicalEmbedding}'::vector"),
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'document_id' => 1, 
            'content' => 'Similar content', 
            'chunk_index' => 1,
            'embedding' => DB::raw("'{$similarEmbedding}'::vector"),
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'document_id' => 1, 
            'content' => 'Different content', 
            'chunk_index' => 2,
            'embedding' => DB::raw("'{$differentEmbedding}'::vector"),
            'created_at' => now(),
            'updated_at' => now()
        ]
    ]);
    
    // Query with similarity search
    $results = DB::table('document_embeddings')
             ->select(
                 'id',
                 'content',
                 'chunk_index',
                 DB::raw("embedding <-> '{$identicalEmbedding}'::vector AS distance")
             )
             ->orderBy('distance')
             ->get();
    
    return response()->json([
        'query_embedding' => '1536-dimensional vector with all 0.42 values',
        'results' => $results
    ]);
});


// routes/web.php
// Route::post('/ask', function () {
//     return view('school-assistant');
// })->name('ask');
// Route::post('/ask', [SchoolAssistantController::class, 'ask'])->name('ask');

Route::get('/upload', function () {
    return view('upload');
} );
Route::get('/chatpage', function () {
    return view('chat');
} );