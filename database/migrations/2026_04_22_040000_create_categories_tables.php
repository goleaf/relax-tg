<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('focus_problems', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('experience_levels', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('module_choices', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('meditation_types', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meditation_types');
        Schema::dropIfExists('module_choices');
        Schema::dropIfExists('experience_levels');
        Schema::dropIfExists('focus_problems');
    }
};
