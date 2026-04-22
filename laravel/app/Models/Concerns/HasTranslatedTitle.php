<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-require-extends Model
 */
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
        $titleAttribute = $this->getAttribute('title');
        $title = is_array($titleAttribute) ? $titleAttribute : [];
        $locale ??= app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackLocale = is_string($fallbackLocale) ? $fallbackLocale : 'en';

        if (isset($title[$locale]) && is_string($title[$locale]) && filled($title[$locale])) {
            return $title[$locale];
        }

        if (isset($title[$fallbackLocale]) && is_string($title[$fallbackLocale]) && filled($title[$fallbackLocale])) {
            return $title[$fallbackLocale];
        }

        foreach ($title as $value) {
            if (is_string($value) && filled($value)) {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForFilamentOptions(Builder $query): Builder
    {
        return $query
            ->orderBy('id')
            ->select(['id', 'title']);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForFilamentIndex(Builder $query): Builder
    {
        return $query
            ->orderBy('id')
            ->select(['id', 'title', 'slug', 'created_at', 'updated_at']);
    }
}
