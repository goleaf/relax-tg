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
                    ->searchable(['name', 'code'])
                    ->icon(fn ($record) => 'flag-language-'.strtolower($record->code)),
                TextColumn::make('native_name')
                    ->label(__('admin.resources.languages.fields.native_name'))
                    ->state(fn (Language $record): string => $record->native_name ?: Language::nativeName($record->code, $record->name))
                    ->searchable(['native_name']),
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
