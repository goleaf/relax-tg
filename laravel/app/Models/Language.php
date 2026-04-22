<?php

namespace App\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Language extends Model
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    public static function nativeName(string $code, ?string $fallback = null): string
    {
        return config("language_native_names.{$code}", $fallback ?? Str::upper($code));
    }

    public static function displayName(string $code, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if (function_exists('locale_get_display_name')) {
            $displayName = locale_get_display_name($code, $locale);

            if (filled($displayName)) {
                return Str::of($displayName)->title()->toString();
            }
        }

        return config("languages.{$code}", Str::upper($code));
    }
}
