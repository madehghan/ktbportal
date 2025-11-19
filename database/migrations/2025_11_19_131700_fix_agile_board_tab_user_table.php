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
        // Check if columns don't exist, then add them
        if (!Schema::hasColumn('agile_board_tab_user', 'agile_board_tab_id')) {
            Schema::table('agile_board_tab_user', function (Blueprint $table) {
                $table->foreignId('agile_board_tab_id')->after('id')->constrained('agile_board_tabs')->onDelete('cascade');
            });
        }
        
        if (!Schema::hasColumn('agile_board_tab_user', 'user_id')) {
            Schema::table('agile_board_tab_user', function (Blueprint $table) {
                $table->foreignId('user_id')->after('agile_board_tab_id')->constrained('users')->onDelete('cascade');
            });
        }
        
        // Add unique constraint
        try {
            Schema::table('agile_board_tab_user', function (Blueprint $table) {
                $table->unique(['agile_board_tab_id', 'user_id'], 'agile_board_tab_user_unique');
            });
        } catch (\Exception $e) {
            // Unique constraint might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agile_board_tab_user', function (Blueprint $table) {
            $table->dropUnique(['agile_board_tab_id', 'user_id']);
            $table->dropForeign(['agile_board_tab_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['agile_board_tab_id', 'user_id']);
        });
    }
};
