<?php

use App\Http\Controllers\SchoolAssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
// authentification routes


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Authenticated routes (require login)
Route::middleware('auth:sanctum')->group(function () {
    // Regular user routes
    Route::post('/school-assistant/ask', [SchoolAssistantController::class, 'askQuestion']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::post('/convert_to_Admin', [AuthController::class, 'convertToAdmin']);
        Route::get('/school-assistant/documents', [SchoolAssistantController::class, 'listDocuments']);
        Route::post('/school-assistant/upload', [SchoolAssistantController::class, 'uploadDocument']);
        Route::delete('/school-assistant/documents', [SchoolAssistantController::class, 'deleteDocumentAll']);
        Route::get('/school-assistant/documents/{id}', [SchoolAssistantController::class, 'getDocument']);
        Route::delete('/school-assistant/documents/{id}', [SchoolAssistantController::class, 'deleteDocument']);
    });
    
    // User routes
    Route::get('/auth/user', [AuthController::class, 'getUser']);
    // Logout (requires auth)
    Route::post('/logout', [AuthController::class, 'logout']);


    // Conversation routes
    Route::post('/conversation', [ConversationController::class, 'store']);
    Route::get('/conversation/{id}', [ConversationController::class, 'getConversation']);
    Route::put('/conversation/{id}', [ConversationController::class, 'update']);
    Route::delete('/conversation/{id}', [ConversationController::class, 'deleteConversation']);
    Route::post('/conversation/{id}/line', [ConversationController::class, 'addLineConversation']);
});

// routes/api.php
Route::get('/debug-cookie', function(Request $request) {
    return response()->json([
        'cookie_received' => $request->cookie('auth_token'),
        'user_authenticated' => auth()->check(),
        'session_id' => session()->getId()
    ]);
});