<?php

namespace App\Filament\Resources\Practices\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::where('is_enabled', true)->get();

        $tabs = $languages->map(function ($language) {
            return Tabs\Tab::make($language->name)
                ->schema([
                    TextInput::make("title.{$language->code}")
                        ->label('Title ('.strtoupper($language->code).')')
                        ->required($language->code === 'en'),
                    Textarea::make("description.{$language->code}")
                        ->label('Description ('.strtoupper($language->code).')')
                        ->rows(5),
                ]);
        })->toArray();

        return $schema
            ->components([
                Tabs::make('Languages')
                    ->tabs($tabs)
                    ->columnSpanFull(),
            ]);
    }
}
