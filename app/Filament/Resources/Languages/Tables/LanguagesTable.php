<?php

namespace App\Filament\Resources\Languages\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class LanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('is_enabled', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->icon(fn ($record) => 'flag-language-'.strtolower($record->code)),
                ToggleColumn::make('is_enabled'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ]);
    }
}
