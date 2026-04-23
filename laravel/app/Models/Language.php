<?php

namespace App\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $native_name
 * @property bool $is_enabled
 */
class Language extends Model
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    private const SUPPORTED_INTERFACE_LOCALES = ['de', 'en', 'es', 'fr', 'it', 'lt', 'pl', 'ru', 'uk'];

    private const CONTENT_CACHE_VERSION_KEY = 'language:content-cache-version';

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Language $language): void {
            if (filled($language->native_name)) {
                return;
            }

            $code = trim($language->code);

            if ($code === '') {
                return;
            }

            $fallbackName = filled($language->name)
                ? $language->name
                : null;

            $language->native_name = self::nativeName($code, $fallbackName);
        });
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForFilamentIndex(Builder $query): Builder
    {
        return $query->select([
            'id',
            'code',
            'name',
            'native_name',
            'is_enabled',
        ]);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForEnabledContentTabs(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'code',
                'name',
                'native_name',
                'is_enabled',
            ])
            ->where('is_enabled', true)
            ->orderBy('name')
            ->orderBy('code');
    }

    /**
     * @return array<int, string>
     */
    public static function supportedInterfaceLocales(): array
    {
        return self::SUPPORTED_INTERFACE_LOCALES;
    }

    public static function nativeName(string $code, ?string $fallback = null): string
    {
        $nativeName = config("language_native_names.{$code}", $fallback ?? Str::upper($code));

        return is_string($nativeName) ? $nativeName : ($fallback ?? Str::upper($code));
    }

    public static function displayName(string $code, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if (function_exists('locale_get_display_name')) {
            $displayName = locale_get_display_name($code, $locale);

            if (is_string($displayName) && filled($displayName)) {
                return Str::of($displayName)->title()->toString();
            }
        }

        $language = config("languages.{$code}", Str::upper($code));

        return is_string($language) ? $language : Str::upper($code);
    }

    public static function bumpContentCacheVersion(): void
    {
        if (Cache::add(self::CONTENT_CACHE_VERSION_KEY, 1, now()->addYears(5))) {
            return;
        }

        Cache::increment(self::CONTENT_CACHE_VERSION_KEY);
    }

    /**
     * @return list<array{
     *     id: int,
     *     code: string,
     *     name: string,
     *     native_name: string|null,
     *     is_enabled: bool
     * }>
     */
    public static function enabledContentTabs(): array
    {
        return Cache::remember(
            self::contentCacheKey('enabled-content-tabs'),
            now()->addMinutes(30),
            fn (): array => array_values(static::query()
                ->forEnabledContentTabs()
                ->get()
                ->map(fn (Language $language): array => [
                    'id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                    'native_name' => $language->native_name,
                    'is_enabled' => $language->is_enabled,
                ])
                ->all()),
        );
    }

    /**
     * @return list<string>
     */
    public static function enabledCodes(): array
    {
        return Cache::remember(
            self::contentCacheKey('enabled-codes'),
            now()->addMinutes(30),
            fn (): array => array_values(static::query()
                ->enabled()
                ->orderBy('id')
                ->pluck('code')
                ->filter(fn (mixed $code): bool => is_string($code) && ($code !== ''))
                ->values()
                ->all()),
        );
    }

    public static function enabledCount(): int
    {
        return Cache::remember(
            self::contentCacheKey('enabled-count'),
            now()->addMinutes(30),
            fn (): int => static::query()
                ->enabled()
                ->count(),
        );
    }

    public function flagIcon(): string
    {
        return 'flag-language-'.Str::lower($this->code);
    }

    private static function contentCacheKey(string $suffix): string
    {
        $version = Cache::get(self::CONTENT_CACHE_VERSION_KEY);

        if (! is_numeric($version)) {
            Cache::forever(self::CONTENT_CACHE_VERSION_KEY, 1);

            $version = 1;
        }

        return "language:content:{$suffix}:v{$version}";
    }
}
