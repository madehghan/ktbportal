<?php

namespace App\Console\Commands;

use App\Services\BotService;
use Illuminate\Console\Command;

class InitializeBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ایجاد ربات کارمانیا توسعه و برقراری مکالمه با همه کاربران';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('در حال ایجاد ربات کارمانیا توسعه...');
        
        BotService::initializeBotForAllUsers();
        
        $this->info('✅ ربات کارمانیا توسعه ایجاد شد و مکالمه با همه کاربران برقرار شد.');
        
        return Command::SUCCESS;
    }
}
