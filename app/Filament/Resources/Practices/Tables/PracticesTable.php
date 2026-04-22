<?php

namespace App\Filament\Resources\Practices\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make('focusProblem.title.'.$locale)
                    ->label('Focus Problem')
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->width('150px'),
                TextColumn::make('experienceLevel.title.'.$locale)
                    ->label('Level')
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->width('150px'),
                TextColumn::make('moduleChoice.title.'.$locale)
                    ->label('Module')
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->width('150px'),
                TextColumn::make('meditationType.title.'.$locale)
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
                SelectFilter::make('focus_problem_id')
                    ->label('Focus Problem')
                    ->relationship('focusProblem', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('experience_level_id')
                    ->label('Experience Level')
                    ->relationship('experienceLevel', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('module_choice_id')
                    ->label('Module Choice')
                    ->relationship('moduleChoice', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('meditation_type_id')
                    ->label('Meditation Type')
                    ->relationship('meditationType', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTitle(app()->getLocale()))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('day')
                    ->options(array_combine(range(1, 29), array_map(fn ($i) => "{$i} Day", range(1, 29)))),
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
