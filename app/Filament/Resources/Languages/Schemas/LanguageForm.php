<?php

namespace App\Filament\Resources\Languages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        $languageOptions = collect(config('languages'))->mapWithKeys(function ($name, $code) {
            $flag = '<x-flag-language-'.strtolower($code).' class="w-4 h-4 inline-block mr-2 align-middle" />';

            return [$code => Blade::render($flag).' '.$name];
        })->toArray();

        return $schema
            ->components([
                Section::make('Language Details')
                    ->schema([
                        Select::make('code')
                            ->options($languageOptions)
                            ->allowHtml()
                            ->searchable()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('name', config('languages')[$state] ?? null)),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_enabled')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
