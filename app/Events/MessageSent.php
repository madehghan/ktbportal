<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender', 'attachments', 'conversation']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;
        $otherUserId = $conversation->user1_id === $this->message->sender_id 
            ? $conversation->user2_id 
            : $conversation->user1_id;
        
        return [
            new PrivateChannel('user.' . $otherUserId),
            new PrivateChannel('conversation.' . $conversation->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                ],
                'body' => $this->message->body,
                'type' => $this->message->type,
                'is_read' => $this->message->is_read,
                'created_at' => $this->message->created_at->toIso8601String(),
                'attachments' => $this->message->attachments->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'original_name' => $attachment->original_name,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'url' => $attachment->url,
                    ];
                }),
            ],
        ];
    }
}
