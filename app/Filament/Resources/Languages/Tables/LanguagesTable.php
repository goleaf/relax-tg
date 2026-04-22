<?php

namespace App\Filament\Resources\Languages\Tables;

use App\Models\Language;
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
                    ->label(__('admin.resources.languages.fields.name'))
                    ->formatStateUsing(fn (Language $record): string => Language::displayName($record->code))
                    ->searchable()
                    ->icon(fn ($record) => 'flag-language-'.strtolower($record->code)),
                ToggleColumn::make('is_enabled')
                    ->label(__('admin.resources.languages.fields.is_enabled')),

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
