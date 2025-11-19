<?php

namespace App\Observers;

use App\Models\User;
use App\Services\BotService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Create conversation with bot for new user
        $bot = BotService::getBotUser();
        
        // Don't create conversation if this is the bot itself
        if ($user->id === $bot->id) {
            return;
        }
        
        $conversation = BotService::getOrCreateConversation($user->id);
        
        // Send welcome message
        $welcomeMessage = "👋 سلام {$user->name}!\n\n";
        $welcomeMessage .= "من ربات کارمانیا توسعه هستم و از این به بعد تمام اعلان‌ها و رویدادهای مربوط به پروژه‌ها و تسک‌ها را برای شما ارسال خواهم کرد.\n\n";
        $welcomeMessage .= "💡 شما می‌توانید از طریق این ربات از:\n";
        $welcomeMessage .= "• اضافه شدن به پروژه‌ها\n";
        $welcomeMessage .= "• اختصاص تسک‌ها\n";
        $welcomeMessage .= "• و سایر رویدادهای سیستم\n";
        $welcomeMessage .= "مطلع شوید.\n\n";
        $welcomeMessage .= "موفق باشید! 🚀";
        
        BotService::sendMessage($user->id, $welcomeMessage);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
