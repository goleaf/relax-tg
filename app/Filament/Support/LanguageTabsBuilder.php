<?php

namespace App\Filament\Support;

use App\Models\Language;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

/**
 * Builds Filament Tabs for all enabled languages, with country flag icons in tab labels.
 *
 * Usage:
 *   LanguageTabsBuilder::make(function (Language $language) {
 *       return [
 *           TextInput::make("title.{$language->code}"),
 *       ];
 *   })
 *
 * The builder queries Language::where('is_enabled', true) automatically.
 * Each tab label is rendered as: 🏳 <LanguageName> using the outhebox/blade-flags package.
 *
 * @see https://github.com/outhebox/blade-flags
 */
class LanguageTabsBuilder
{
    /**
     * Build a Tabs component populated with one tab per enabled language.
     *
     * @param  callable(Language): array<int, mixed>  $fieldFactory  Returns an array of Filament form components for the given language.
     */
    public static function make(callable $fieldFactory): Tabs
    {
        $languages = Language::where('is_enabled', true)->get();

        $tabs = $languages->map(function (Language $language) use ($fieldFactory) {
            $label = static::tabLabel($language);

            return Tabs\Tab::make($language->code)
                ->label($label)
                ->schema($fieldFactory($language));
        })->toArray();

        return Tabs::make('Languages')
            ->tabs($tabs)
            ->columnSpanFull();
    }

    /**
     * Render the tab label with the country flag for the given language.
     *
     * Returns an HtmlString so Filament renders the SVG flag without escaping.
     * Uses the outhebox/blade-flags package: <x-flag-language-{code} />.
     */
    public static function tabLabel(Language $language): HtmlString
    {
        $flagComponent = '<x-flag-language-'.strtolower($language->code).' class="w-4 h-4 inline-block mr-1 align-middle" />';
        $flag = Blade::render($flagComponent);

        return new HtmlString($flag.' '.e($language->name));
    }
}
