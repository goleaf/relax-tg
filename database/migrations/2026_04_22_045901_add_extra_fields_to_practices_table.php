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
        Schema::table('practices', function (Blueprint $table) {
            $table->string('focus_problem')->default('focus')->after('day');
            $table->string('experience_level')->default('beginner')->after('focus_problem');
            $table->string('module_choice')->default('main')->after('experience_level');
            $table->string('meditation_type')->default('breath')->after('module_choice');
            $table->unsignedInteger('duration')->default(0)->after('meditation_type');
            $table->string('image_url')->nullable()->after('duration');
            $table->string('video_url')->nullable()->after('image_url');
            $table->boolean('is_active')->default(true)->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn([
                'focus_problem',
                'experience_level',
                'module_choice',
                'meditation_type',
                'duration',
                'image_url',
                'video_url',
                'is_active',
            ]);
        });
    }
};
