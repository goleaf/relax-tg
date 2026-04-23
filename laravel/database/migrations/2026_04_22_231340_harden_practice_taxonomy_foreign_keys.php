<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('practices', function (Blueprint $table): void {
            $table->dropForeign(['focus_problem_id']);
            $table->dropForeign(['experience_level_id']);
            $table->dropForeign(['module_choice_id']);
            $table->dropForeign(['meditation_type_id']);
        });

        Schema::table('practices', function (Blueprint $table): void {
            $table->foreign('focus_problem_id')->references('id')->on('focus_problems')->restrictOnDelete();
            $table->foreign('experience_level_id')->references('id')->on('experience_levels')->restrictOnDelete();
            $table->foreign('module_choice_id')->references('id')->on('module_choices')->restrictOnDelete();
            $table->foreign('meditation_type_id')->references('id')->on('meditation_types')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table): void {
            $table->dropForeign(['focus_problem_id']);
            $table->dropForeign(['experience_level_id']);
            $table->dropForeign(['module_choice_id']);
            $table->dropForeign(['meditation_type_id']);
        });

        Schema::table('practices', function (Blueprint $table): void {
            $table->foreign('focus_problem_id')->references('id')->on('focus_problems')->nullOnDelete();
            $table->foreign('experience_level_id')->references('id')->on('experience_levels')->nullOnDelete();
            $table->foreign('module_choice_id')->references('id')->on('module_choices')->nullOnDelete();
            $table->foreign('meditation_type_id')->references('id')->on('meditation_types')->nullOnDelete();
        });
    }
};
