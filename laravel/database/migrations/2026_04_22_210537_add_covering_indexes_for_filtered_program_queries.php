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
        Schema::table('practices', function (Blueprint $table): void {
            $table->dropIndex(['focus_problem_id']);
            $table->dropIndex(['experience_level_id']);
            $table->dropIndex(['module_choice_id']);
            $table->dropIndex(['meditation_type_id']);

            $table->index(['focus_problem_id', 'day', 'id']);
            $table->index(['experience_level_id', 'day', 'id']);
            $table->index(['module_choice_id', 'day', 'id']);
            $table->index(['meditation_type_id', 'day', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table): void {
            $table->dropIndex(['focus_problem_id', 'day', 'id']);
            $table->dropIndex(['experience_level_id', 'day', 'id']);
            $table->dropIndex(['module_choice_id', 'day', 'id']);
            $table->dropIndex(['meditation_type_id', 'day', 'id']);

            $table->index('focus_problem_id');
            $table->index('experience_level_id');
            $table->index('module_choice_id');
            $table->index('meditation_type_id');
        });
    }
};
