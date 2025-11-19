<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'project_id',
        'user1_id',
        'user2_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the first user in the conversation.
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the conversation.
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get the project this conversation belongs to (for group chats).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the other user in the conversation.
     */
    public function getOtherUser(int $currentUserId): ?User
    {
        if ($this->user1_id === $currentUserId) {
            return $this->user2;
        }
        return $this->user1;
    }

    /**
     * Get or create a conversation between two users.
     */
    public static function getOrCreate(int $user1Id, int $user2Id): self
    {
        // Ensure user1_id is always smaller than user2_id for consistency
        if ($user1Id > $user2Id) {
            [$user1Id, $user2Id] = [$user2Id, $user1Id];
        }

        return static::firstOrCreate(
            [
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
                'project_id' => null,
            ]
        );
    }

    /**
     * Get or create a project group conversation.
     */
    public static function getOrCreateProjectChat(int $projectId): self
    {
        return static::firstOrCreate(
            [
                'project_id' => $projectId,
            ],
            [
                'user1_id' => null,
                'user2_id' => null,
            ]
        );
    }
}
