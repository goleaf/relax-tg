<?php

namespace App\Filament\Resources\Languages\Tables;

use App\Models\Language;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderByDesc('is_enabled')
                ->orderBy('code'))
            ->columns([
                ViewColumn::make('name')
                    ->label(__('admin.resources.languages.fields.name'))
                    ->searchable(['name', 'code'])
                    ->view('filament.tables.columns.language-name'),
                TextColumn::make('native_name')
                    ->label(__('admin.resources.languages.fields.native_name'))
                    ->state(fn (Language $record): string => filled($record->native_name)
                        ? $record->native_name
                        : Language::nativeName($record->code, $record->name))
                    ->searchable(['native_name'])
                    ->icon(Heroicon::OutlinedLanguage)
                    ->iconColor('primary'),
                ToggleColumn::make('is_enabled')
                    ->label(__('admin.resources.languages.fields.is_enabled'))
                    ->onIcon(Heroicon::OutlinedCheckCircle)
                    ->offIcon(Heroicon::OutlinedXCircle),

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
