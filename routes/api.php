<?php

use App\Http\Controllers\SchoolAssistantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->group(function () {
    // API endpoint for asking questions
    Route::post('/school-assistant/ask', [SchoolAssistantController::class, 'askQuestion']);
    
    // API endpoint for listing documents
    Route::get('/school-assistant/documents', [SchoolAssistantController::class, 'listDocuments']);
    
    // Admin-only endpoints
    // Route::middleware('admin')->group(function () {
        // Upload document
    Route::post('/school-assistant/upload', [SchoolAssistantController::class, 'uploadDocument']);
        
        // Delete document
    Route::delete('/school-assistant/documents/{id}', [SchoolAssistantController::class, 'deleteDocument']);
    // });
// });