<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class BotService
{
    const BOT_NAME = 'ربات کارمانیا توسعه';
    const BOT_EMAIL = 'bot@karmania.dev';
    const BOT_MOBILE = '00000000000';

    /**
     * Get or create the bot user.
     */
    public static function getBotUser(): User
    {
        $bot = User::where('email', self::BOT_EMAIL)->first();
        
        if (!$bot) {
            $bot = User::create([
                'name' => self::BOT_NAME,
                'email' => self::BOT_EMAIL,
                'mobile' => self::BOT_MOBILE,
                'password' => bcrypt(uniqid()), // Random password, bot won't login
            ]);
        }
        
        return $bot;
    }

    /**
     * Get or create conversation between bot and a user.
     */
    public static function getOrCreateConversation(int $userId): Conversation
    {
        $bot = self::getBotUser();
        return Conversation::getOrCreate($bot->id, $userId);
    }

    /**
     * Send a message from bot to a user.
     */
    public static function sendMessage(int $userId, string $message, string $type = 'text'): Message
    {
        $bot = self::getBotUser();
        $conversation = self::getOrCreateConversation($userId);
        
        $messageModel = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $bot->id,
            'body' => $message,
            'type' => $type,
        ]);
        
        // Update conversation last message time
        $conversation->update([
            'last_message_at' => now(),
        ]);
        
        // Broadcast message event for real-time updates
        try {
            broadcast(new \App\Events\MessageSent($messageModel))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting not configured, continue without it
        }
        
        return $messageModel;
    }

    /**
     * Initialize bot conversations for all existing users.
     */
    public static function initializeBotForAllUsers(): void
    {
        $bot = self::getBotUser();
        $users = User::where('id', '!=', $bot->id)->get();
        
        foreach ($users as $user) {
            $conversation = self::getOrCreateConversation($user->id);
            
            // Send welcome message if conversation is new (no messages yet)
            if ($conversation->messages()->count() === 0) {
                $welcomeMessage = "👋 سلام {$user->name}!\n\n";
                $welcomeMessage .= "من ربات کارمانیا توسعه هستم و از این به بعد تمام اعلان‌ها و رویدادهای مربوط به پروژه‌ها و تسک‌ها را برای شما ارسال خواهم کرد.\n\n";
                $welcomeMessage .= "💡 شما می‌توانید از طریق این ربات از:\n";
                $welcomeMessage .= "• اضافه شدن به پروژه‌ها\n";
                $welcomeMessage .= "• اختصاص تسک‌ها\n";
                $welcomeMessage .= "• و سایر رویدادهای سیستم\n";
                $welcomeMessage .= "مطلع شوید.\n\n";
                $welcomeMessage .= "موفق باشید! 🚀";
                
                self::sendMessage($user->id, $welcomeMessage);
            }
        }
    }
}

