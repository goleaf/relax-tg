<?php

namespace App\Filament\Resources\Practices\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::where('is_enabled', true)->get();

        $tabs = $languages->map(function (Language $language) {
            return Tabs\Tab::make($language->name)
                ->schema([
                    TextInput::make("title.{$language->code}")
                        ->label('Title')
                        ->required($language->code === 'en')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make("description.{$language->code}")
                        ->label('Description')
                        ->rows(6)
                        ->columnSpanFull(),
                ]);
        })->toArray();

        return $schema
            ->components([
                Section::make('Translations')
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs($tabs)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
