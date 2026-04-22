<?php

namespace App\Models;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

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

    /**
     * @return list<string>
     */
    public static function enabledCodes(): array
    {
        return array_values(static::query()
            ->enabled()
            ->pluck('code')
            ->filter(fn (mixed $code): bool => is_string($code) && ($code !== ''))
            ->values()
            ->all());
    }

    public function flagIcon(): string
    {
        return 'flag-language-'.Str::lower($this->code);
    }
}
