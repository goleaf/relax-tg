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
        Schema::create('practices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('day');

            $table->foreignId('focus_problem_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('experience_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('module_choice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('meditation_type_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedInteger('duration')->default(0);
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_active')->default(true);

            $table->json('title');
            $table->json('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
