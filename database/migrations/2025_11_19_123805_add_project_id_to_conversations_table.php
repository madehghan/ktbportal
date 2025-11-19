<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add project_id first
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')->constrained('projects')->onDelete('cascade');
        });
        
        // Make user columns nullable using raw SQL
        // MySQL allows multiple NULL values in unique constraints, so we can keep the existing unique constraint
        DB::statement('ALTER TABLE conversations MODIFY user1_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE conversations MODIFY user2_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop unique constraint
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique('conversations_user1_user2_unique');
        });
        
        // Drop project_id
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
        
        // Make user columns NOT NULL using raw SQL
        DB::statement('ALTER TABLE conversations MODIFY user1_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE conversations MODIFY user2_id BIGINT UNSIGNED NOT NULL');
        
        // Re-add original unique constraint
        Schema::table('conversations', function (Blueprint $table) {
            $table->unique(['user1_id', 'user2_id']);
        });
    }
};
