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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // webservice, pattern, verification, welcome
            $table->string('to_number'); // recipient number
            $table->string('from_number'); // sender number
            $table->text('message')->nullable(); // message content
            $table->json('params')->nullable(); // additional parameters
            $table->string('status'); // pending, sent, failed
            $table->text('response')->nullable(); // API response
            $table->string('message_outbox_id')->nullable(); // IPPanel message ID
            $table->string('error_message')->nullable(); // error message if failed
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // who triggered the SMS
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('type');
            $table->index('to_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
