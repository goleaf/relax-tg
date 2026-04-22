<?php

namespace App\Filament\Support;

use App\Models\Language;
use Filament\Schemas\Components\Tabs;

/**
 * Builds Filament Tabs for all enabled languages, sorted alphabetically by name.
 *
 * Usage:
 *   LanguageTabsBuilder::make(function (Language $language) {
 *       return [
 *           TextInput::make("title.{$language->code}"),
 *       ];
 *   })
 *
 * The builder queries enabled languages automatically.
 * Tabs are sorted ASC by the language name.
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
        $languages = Language::query()
            ->forEnabledContentTabs()
            ->get();

        $tabs = $languages->map(function (Language $language) use ($fieldFactory) {
            return Tabs\Tab::make($language->code)
                ->label(Language::displayName($language->code))
                ->schema($fieldFactory($language));
        })->toArray();

        return Tabs::make(__('admin.resources.languages.navigation'))
            ->tabs($tabs)
            ->columnSpanFull();
    }
}
