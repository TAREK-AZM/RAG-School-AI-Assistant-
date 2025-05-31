<?php

use App\Http\Controllers\SchoolAssistantController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

// Public routes
Route::get('/school-assistant', [SchoolAssistantController::class, 'index'])->name('school-assistant');

    // Chat page
Route::get('/chatpage', function () {
    return view('chat');
} );
    // Chat Ask questions
Route::post('/school-assistant/ask', [SchoolAssistantController::class, 'askQuestion'])->name('school-assistant.ask');
    
Route::get('/school-assistant/dashboard', function () {
    return view('admin-dashboard');
} )->name('school-assistant.dashboard');
Route::get('/school-assistant/documents', [SchoolAssistantController::class, 'listDocuments'])->name('school-assistant.documents');

    // View documents dashboard
Route::get('/school-assistant/documents', [SchoolAssistantController::class, 'listDocuments'])->name('school-assistant.documents');

    // Upload documents page
Route::get('/upload', function () {
    return view('upload');// 
} );
    // Upload documents
Route::post('/school-assistant/upload', [SchoolAssistantController::class, 'uploadDocument'])->name('school-assistant.upload');
    
    // Get document
// Route::get('/school-assistant/documents/{id}', [SchoolAssistantController::class, 'getDocument'])->name('school-assistant.getDocument');

  // Delete document
  Route::post('/school-assistant/documents/{id}', [SchoolAssistantController::class, 'deleteDocument'])->name('school-assistant.delete');


