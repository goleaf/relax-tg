<?php

namespace App\Filament\Resources\Practices\Schemas;

use App\Filament\Support\LanguageTabsBuilder;
use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->schema([
                        Select::make('day')
                            ->options(array_combine(range(1, 29), array_map(fn ($i) => "{$i} Day", range(1, 29))))
                            ->required()
                            ->default(fn () => request()->query('day')),
                    ]),
                Section::make('Translations')
                    ->schema([
                        LanguageTabsBuilder::make(function (Language $language) {
                            return [
                                TextInput::make("title.{$language->code}")
                                    ->label('Title')
                                    ->required($language->code === 'en')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make("description.{$language->code}")
                                    ->label('Description')
                                    ->rows(6)
                                    ->columnSpanFull(),
                            ];
                        }),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
