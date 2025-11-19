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
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('priority');
            $table->timestamp('completed_at')->nullable()->after('is_completed');
            $table->foreignId('completed_by')->nullable()->after('completed_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['is_completed', 'completed_at', 'completed_by']);
        });
    }
};
