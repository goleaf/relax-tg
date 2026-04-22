<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasTranslatedTitle
{
    protected function initializeHasTranslatedTitle(): void
    {
        $this->mergeCasts([
            'title' => 'array',
        ]);
    }

    public static function titleAttribute(?string $locale = null): string
    {
        return 'title.'.($locale ?? app()->getLocale());
    }

    public function getTitle(?string $locale = null): string
    {
        $title = is_array($this->title) ? $this->title : [];
        $locale ??= app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');

        return $title[$locale]
            ?? $title[$fallbackLocale]
            ?? collect($title)
                ->first(fn (?string $value): bool => filled($value), '')
            ?? '';
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }

    public function scopeForFilamentOptions(Builder $query): Builder
    {
        return $query
            ->ordered()
            ->select(['id', 'title']);
    }

    public function scopeForFilamentIndex(Builder $query): Builder
    {
        return $query
            ->ordered()
            ->select(['id', 'title', 'slug', 'created_at', 'updated_at']);
    }
}
