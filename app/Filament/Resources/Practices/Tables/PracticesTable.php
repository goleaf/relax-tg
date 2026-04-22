<?php

namespace App\Filament\Resources\Practices\Tables;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PracticesTable
{
    /**
     * @param  array<int, string>  $hiddenColumns
     * @param  array<int, string>  $excludedFilters
     * @param  array<int, mixed>  $headerActions
     */
    public static function configure(
        Table $table,
        array $hiddenColumns = [],
        array $excludedFilters = [],
        array $headerActions = [],
    ): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('day')
                    ->label('Day')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => Practice::formatDay($state))
                    ->width('100px'),
                TextColumn::make(Practice::translatedAttribute('title'))
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->width('250px'),
                TextColumn::make(Practice::translatedAttribute('description'))
                    ->label('Description')
                    ->hidden()
                    ->limit(80)
                    ->wrap()
                    ->width('320px'),
                TextColumn::make('focusProblem.'.FocusProblem::titleAttribute())
                    ->label('Focus Problem')
                    ->hidden(in_array('focus_problem_id', $hiddenColumns, true))
                    ->badge()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('experienceLevel.'.ExperienceLevel::titleAttribute())
                    ->label('Level')
                    ->hidden(in_array('experience_level_id', $hiddenColumns, true))
                    ->badge()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('moduleChoice.'.ModuleChoice::titleAttribute())
                    ->label('Module')
                    ->hidden(in_array('module_choice_id', $hiddenColumns, true))
                    ->badge()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('meditationType.'.MeditationType::titleAttribute())
                    ->label('Type')
                    ->hidden(in_array('meditation_type_id', $hiddenColumns, true))
                    ->badge()
                    ->sortable()
                    ->width('120px'),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(fn (int $state): string => Practice::formatDuration($state))
                    ->sortable()
                    ->width('100px'),
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk(Practice::mediaDisk())
                    ->visibility('public')
                    ->square()
                    ->width(80)
                    ->height(80),
                TextColumn::make('video_path')
                    ->label('Video')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? basename($state) : 'No video')
                    ->url(fn (Practice $record): ?string => $record->getVideoUrl())
                    ->openUrlInNewTab(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable()
                    ->width('80px'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->width('150px'),
            ])
            ->filters(array_values(array_filter([
                static::dayFilter(
                    excluded: in_array('day', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'focus_problem_id',
                    label: 'Focus Problem',
                    relationship: 'focusProblem',
                    excluded: in_array('focus_problem_id', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'experience_level_id',
                    label: 'Experience Level',
                    relationship: 'experienceLevel',
                    excluded: in_array('experience_level_id', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'module_choice_id',
                    label: 'Module Choice',
                    relationship: 'moduleChoice',
                    excluded: in_array('module_choice_id', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'meditation_type_id',
                    label: 'Meditation Type',
                    relationship: 'meditationType',
                    excluded: in_array('meditation_type_id', $excludedFilters, true),
                ),
            ], fn (mixed $filter): bool => $filter !== null)), layout: FiltersLayout::AboveContent)
            ->filtersFormColumns([
                'md' => 2,
                'xl' => 3,
                '2xl' => 5,
            ])
            ->deferFilters(false)
            ->headerActions($headerActions)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    private static function dayFilter(bool $excluded = false): ?SelectFilter
    {
        if ($excluded) {
            return null;
        }

        return SelectFilter::make('day')
            ->label('Day')
            ->options(Practice::dayOptionsWithCounts());
    }

    private static function taxonomyFilter(
        string $field,
        string $label,
        string $relationship,
        bool $excluded = false,
    ): ?SelectFilter {
        if ($excluded) {
            return null;
        }

        return SelectFilter::make($field)
            ->label($label)
            ->relationship(
                $relationship,
                'id',
                fn (Builder $query) => $query
                    ->forFilamentOptions()
                    ->withCount('practices'),
            )
            ->getOptionLabelFromRecordUsing(
                fn ($record): string => Practice::formatCountedLabel(
                    $record->getTitle(app()->getLocale()),
                    (int) $record->practices_count,
                ),
            )
            ->searchable()
            ->preload();
    }
}
