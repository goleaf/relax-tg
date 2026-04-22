<?php

namespace App\Filament\Resources\Practices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PracticesTable
{
    public static function configure(Table $table): Table
    {
        $locale = app()->getLocale();

        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('day')
                    ->label('Day')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => "{$state} Day")
                    ->width('100px'),
                TextColumn::make("title.{$locale}")
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->width('250px'),
                TextColumn::make("description.{$locale}")
                    ->label('Description')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('400px'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->width('150px'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
