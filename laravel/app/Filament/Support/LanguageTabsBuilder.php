<?php

namespace App\Filament\Support;

use App\Models\Language;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Illuminate\Contracts\Support\Htmlable;

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
     * @param  callable(Language): array<int, Component|Action|ActionGroup|string|Htmlable>  $fieldFactory  Returns an array of Filament form components for the given language.
     */
    public static function make(callable $fieldFactory): Tabs
    {
        $languages = Language::query()
            ->forEnabledContentTabs()
            ->get();

        $tabs = [];

        foreach ($languages as $language) {
            $tabs[] = Tabs\Tab::make($language->code)
                ->label(Language::displayName($language->code))
                ->schema($fieldFactory($language));
        }

        return Tabs::make(__('admin.resources.languages.navigation'))
            ->tabs($tabs)
            ->columnSpanFull();
    }
}
