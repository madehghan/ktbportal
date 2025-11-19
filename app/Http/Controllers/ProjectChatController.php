<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectChatController extends Controller
{
    /**
     * Get or create project conversation and return messages.
     */
    public function getMessages(Project $project)
    {
        $currentUser = Auth::user();
        
        // Verify user is a project member
        if (!$project->users->contains($currentUser->id)) {
            return response()->json(['error' => 'شما عضو این پروژه نیستید'], 403);
        }
        
        // Get or create project conversation
        $conversation = Conversation::getOrCreateProjectChat($project->id);
        
        // Mark messages as read for current user
        $conversation->messages()
            ->where('sender_id', '!=', $currentUser->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        // Get all messages with sender and attachments
        $messages = $conversation->messages()
            ->with(['sender', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'sender_avatar' => $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : null,
                    'body' => $message->body,
                    'type' => $message->type,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $message->created_at->format('H:i'),
                    'attachments' => $message->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_name' => $attachment->file_name,
                            'original_name' => $attachment->original_name,
                            'file_type' => $attachment->file_type,
                            'file_size' => $attachment->file_size,
                            'url' => asset('storage/' . $attachment->file_path),
                        ];
                    }),
                ];
            });
        
        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages,
            'project_users' => $project->users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            }),
        ]);
    }

    /**
     * Send a message to project chat.
     */
    public function sendMessage(Request $request, Project $project)
    {
        $request->validate([
            'body' => 'nullable|string',
            'type' => 'required|in:text,voice,file',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);
        
        $currentUser = Auth::user();
        
        // Verify user is a project member
        if (!$project->users->contains($currentUser->id)) {
            return response()->json(['error' => 'شما عضو این پروژه نیستید'], 403);
        }
        
        // Get or create project conversation
        $conversation = Conversation::getOrCreateProjectChat($project->id);
        
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
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'sender_avatar' => $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : null,
                'body' => $message->body,
                'type' => $message->type,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'created_at_formatted' => $message->created_at->format('H:i'),
                'attachments' => $message->attachments->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'original_name' => $attachment->original_name,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'url' => asset('storage/' . $attachment->file_path),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Download attachment.
     */
    public function downloadAttachment($attachmentId)
    {
        $attachment = MessageAttachment::findOrFail($attachmentId);
        $currentUser = Auth::user();
        
        // Verify user is part of project
        $conversation = $attachment->message->conversation;
        if (!$conversation->project) {
            abort(404);
        }
        
        $project = $conversation->project;
        if (!$project->users->contains($currentUser->id)) {
            abort(403);
        }
        
        $filePath = storage_path('app/public/' . $attachment->file_path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        return response()->download($filePath, $attachment->original_name);
    }
}
