<?php

namespace App\Models;

use App\Enums\ExperienceLevel;
use App\Enums\FocusProblem;
use App\Enums\MeditationType;
use App\Enums\ModuleChoice;
use Database\Factories\PracticeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $day
 * @property FocusProblem $focus_problem
 * @property ExperienceLevel $experience_level
 * @property ModuleChoice $module_choice
 * @property MeditationType $meditation_type
 * @property int $duration
 * @property string|null $image_url
 * @property string|null $video_url
 * @property bool $is_active
 * @property array<string, string> $title
 * @property array<string, string>|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Practice extends Model
{
    /** @use HasFactory<PracticeFactory> */
    use HasFactory;

    protected $fillable = [
        'day',
        'focus_problem',
        'experience_level',
        'module_choice',
        'meditation_type',
        'duration',
        'image_url',
        'video_url',
        'is_active',
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'focus_problem' => FocusProblem::class,
            'experience_level' => ExperienceLevel::class,
            'module_choice' => ModuleChoice::class,
            'meditation_type' => MeditationType::class,
            'is_active' => 'boolean',
            'title' => 'array',
            'description' => 'array',
        ];
    }

    /**
     * Get the translated title for the given locale, falling back to English.
     */
    public function getTitle(string $locale = 'en'): string
    {
        return $this->title[$locale] ?? $this->title['en'] ?? '';
    }

    /**
     * Get the translated description for the given locale, falling back to English.
     */
    public function getDescription(string $locale = 'en'): ?string
    {
        if (empty($this->description)) {
            return null;
        }

        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }

    /**
     * Scope to order practices by newest first.
     *
     * @param  Builder<Practice>  $query
     * @return Builder<Practice>
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('id', 'desc');
    }
}
