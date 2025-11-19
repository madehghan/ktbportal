<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Services\BotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessengerController extends Controller
{
    /**
     * Display the messenger page.
     */
    public function index()
    {
        $currentUser = Auth::user();
        $bot = BotService::getBotUser();
        
        // Get all users except current user and bot
        $users = User::where('id', '!=', $currentUser->id)
            ->where('id', '!=', $bot->id)
            ->orderBy('name')
            ->get();
        
        // Get all conversations for current user
        $conversations = Conversation::where('user1_id', $currentUser->id)
            ->orWhere('user2_id', $currentUser->id)
            ->with(['user1', 'user2', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) use ($currentUser) {
                $otherUser = $conversation->getOtherUser($currentUser->id);
                $lastMessage = $conversation->messages()->latest()->first();
                $unreadCount = $conversation->messages()
                    ->where('sender_id', '!=', $currentUser->id)
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at,
                ];
            });
        
        return view('messenger.index', compact('users', 'conversations'));
    }

    /**
     * Get or create conversation with a user.
     */
    public function getConversation($userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        if ($otherUser->id === $currentUser->id) {
            return response()->json(['error' => 'Cannot create conversation with yourself'], 400);
        }
        
        $conversation = Conversation::getOrCreate($currentUser->id, $otherUser->id);
        
        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        $messages = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'conversation' => $conversation,
            'other_user' => $otherUser,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'nullable|string',
            'type' => 'required|in:text,voice,file',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $currentUser = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);
        
        // Verify user is part of conversation
        if ($conversation->user1_id !== $currentUser->id && $conversation->user2_id !== $currentUser->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $currentUser->id,
            'body' => $request->body,
            'type' => $request->type,
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('messages', $fileName, 'public');
            
            MessageAttachment::create([
                'message_id' => $message->id,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ]);
            
            $message->load('attachments');
        }
        
        // Update conversation last message time
        $conversation->update([
            'last_message_at' => now(),
        ]);
        
        $message->load(['sender', 'attachments']);
        
        // Broadcast message event for real-time updates (if broadcasting is configured)
        try {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting not configured, continue without it
        }
        
        return response()->json([
            'message' => $message,
            'success' => true,
        ]);
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages($conversationId)
    {
        $currentUser = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify user is part of conversation
        if ($conversation->user1_id !== $currentUser->id && $conversation->user2_id !== $currentUser->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        $messages = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json($messages);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead($conversationId)
    {
        $currentUser = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify user is part of conversation
        if ($conversation->user1_id !== $currentUser->id && $conversation->user2_id !== $currentUser->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $conversation->messages()
            ->where('sender_id', '!=', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Download attachment.
     */
    public function downloadAttachment($attachmentId)
    {
        $attachment = MessageAttachment::findOrFail($attachmentId);
        $currentUser = Auth::user();
        
        // Verify user is part of conversation
        $conversation = $attachment->message->conversation;
        if ($conversation->user1_id !== $currentUser->id && $conversation->user2_id !== $currentUser->id) {
            abort(403);
        }
        
        $filePath = storage_path('app/public/' . $attachment->file_path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        return response()->download($filePath, $attachment->original_name);
    }
}
