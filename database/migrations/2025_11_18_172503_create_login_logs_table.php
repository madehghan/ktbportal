<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mobile'); // شماره موبایلی که برای ورود استفاده شده
            $table->string('ip_address')->nullable(); // آدرس IP
            $table->text('user_agent')->nullable(); // User Agent مرورگر
            $table->timestamp('login_at'); // زمان ورود
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('user_id');
            $table->index('mobile');
            $table->index('login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
