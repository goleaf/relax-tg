<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $day
 * @property int|null $focus_problem_id
 * @property int|null $experience_level_id
 * @property int|null $module_choice_id
 * @property int|null $meditation_type_id
 * @property int $duration
 * @property string|null $image_url
 * @property string|null $video_url
 * @property bool $is_active
 * @property array<string, string> $title
 * @property array<string, string>|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read FocusProblem|null $focusProblem
 * @property-read ExperienceLevel|null $experienceLevel
 * @property-read ModuleChoice|null $moduleChoice
 * @property-read MeditationType|null $meditationType
 */
class Practice extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'focus_problem_id',
        'experience_level_id',
        'module_choice_id',
        'meditation_type_id',
        'duration',
        'image_url',
        'video_url',
        'is_active',
        'title',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'title' => 'array',
        'description' => 'array',
    ];

    public function focusProblem(): BelongsTo
    {
        return $this->belongsTo(FocusProblem::class);
    }

    public function experienceLevel(): BelongsTo
    {
        return $this->belongsTo(ExperienceLevel::class);
    }

    public function moduleChoice(): BelongsTo
    {
        return $this->belongsTo(ModuleChoice::class);
    }

    public function meditationType(): BelongsTo
    {
        return $this->belongsTo(MeditationType::class);
    }

    public function getTitle(string $locale = 'en'): string
    {
        return $this->title[$locale] ?? $this->title['en'] ?? '';
    }

    public function getDescription(string $locale = 'en'): ?string
    {
        if (empty($this->description)) {
            return null;
        }

        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('id', 'desc');
    }
}
