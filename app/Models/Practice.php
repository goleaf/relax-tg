<?php

namespace App\Models;

use Database\Factories\PracticeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
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
        'title',
        'description',
    ];

    protected function casts(): array
    {
        return [
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
