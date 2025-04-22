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