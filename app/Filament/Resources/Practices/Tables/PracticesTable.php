<?php

namespace App\Filament\Resources\Practices\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
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
                TextColumn::make('meditation_type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->width('120px'),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(fn (int $state): string => floor($state / 60).':'.str_pad($state % 60, 2, '0', STR_PAD_LEFT))
                    ->sortable()
                    ->width('100px'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable()
                    ->width('80px'),
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
                //
            ]);
    }
}
