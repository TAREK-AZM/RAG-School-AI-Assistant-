<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    
    public function store(Request $request)
    {
        $conversation = new Conversation();
        $conversation->title = $request->title;
        $conversation->user_id = Auth::user()->id;
        $conversation->save();

        return response()->json([
            'message' => 'Conversation created successfully',
            'conversation'=> $conversation
        ], 201);
    }

    public function getConversation($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversationLines = $conversation->ConversationLines;
        return response()->json([
            'message' => 'Conversation found successfully',
            'conversation'=> $conversation,
            'conversationLines'=> $conversationLines
        ], 200);
      
    }



    public function update(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->title = $request->title;
        $conversation->save();

        return response()->json([
            'message' => 'Conversation updated successfully',
            'conversation'=> $conversation
        ], 201);
    }

    public function deleteConversation($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->delete();

        return response()->json([
            'message' => 'Conversation deleted successfully',
        ], 201);
    }

    public function addLineConversation(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversationLine = new ConversationLine();
        $conversationLine->question = $request->question;
        $conversationLine->answer = $request->answer;
        $conversationLine->conversation_id = $conversation->id;
        $conversationLine->save();
        // $conversation->addLineConversation($conversationLine);

        return response()->json([    
            'message' => 'Conversation Line added successfully',
            'conversation'=> $conversation
        ], 201);
    }
}