<?php

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addSlugColumn('focus_problems');
        $this->addSlugColumn('experience_levels');
        $this->addSlugColumn('module_choices');
        $this->addSlugColumn('meditation_types');

        FocusProblem::query()->lazyById()->each(function (FocusProblem $focusProblem): void {
            $focusProblem->slug = $focusProblem->generateUniqueSlug();
            $focusProblem->saveQuietly();
        });

        ExperienceLevel::query()->lazyById()->each(function (ExperienceLevel $experienceLevel): void {
            $experienceLevel->slug = $experienceLevel->generateUniqueSlug();
            $experienceLevel->saveQuietly();
        });

        ModuleChoice::query()->lazyById()->each(function (ModuleChoice $moduleChoice): void {
            $moduleChoice->slug = $moduleChoice->generateUniqueSlug();
            $moduleChoice->saveQuietly();
        });

        MeditationType::query()->lazyById()->each(function (MeditationType $meditationType): void {
            $meditationType->slug = $meditationType->generateUniqueSlug();
            $meditationType->saveQuietly();
        });
    }

    public function down(): void
    {
        $this->dropSlugColumn('meditation_types');
        $this->dropSlugColumn('module_choices');
        $this->dropSlugColumn('experience_levels');
        $this->dropSlugColumn('focus_problems');
    }

    private function addSlugColumn(string $table): void
    {
        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->string('slug')->nullable()->after('title');
            $blueprint->unique('slug');
        });
    }

    private function dropSlugColumn(string $table): void
    {
        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->dropUnique(['slug']);
            $blueprint->dropColumn('slug');
        });
    }
};
