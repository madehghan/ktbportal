<?php

namespace Database\Seeders;

use App\Services\BotService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create bot user and initialize conversations for all users
        BotService::initializeBotForAllUsers();
        
        $this->command->info('ربات کارمانیا توسعه ایجاد شد و مکالمه با همه کاربران برقرار شد.');
    }
}
