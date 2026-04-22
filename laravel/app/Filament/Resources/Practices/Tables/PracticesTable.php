<?php

namespace App\Filament\Resources\Practices\Tables;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
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
    ): Table {
        return $table
            ->defaultSort('day', 'asc')
            ->columns([
                TextColumn::make('day')
                    ->label(__('admin.resources.practices.fields.day'))
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->iconColor('primary')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => Practice::formatDay($state))
                    ->width('100px'),
                TextColumn::make(Practice::translatedAttribute('title'))
                    ->label(__('admin.resources.practices.fields.title'))
                    ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                    ->iconColor('primary')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->width('250px'),
                TextColumn::make(Practice::translatedAttribute('description'))
                    ->label(__('admin.resources.practices.fields.description'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->iconColor('gray')
                    ->hidden()
                    ->limit(80)
                    ->wrap()
                    ->width('320px'),
                TextColumn::make('focusProblem.'.FocusProblem::titleAttribute())
                    ->label(__('admin.resources.practices.fields.focus_problem'))
                    ->hidden(in_array('focus_problem_id', $hiddenColumns, true))
                    ->icon(Heroicon::OutlinedBolt)
                    ->iconColor('warning')
                    ->badge()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('experienceLevel.'.ExperienceLevel::titleAttribute())
                    ->label(__('admin.resources.practices.short_labels.experience_level'))
                    ->hidden(in_array('experience_level_id', $hiddenColumns, true))
                    ->icon(Heroicon::OutlinedChartBar)
                    ->iconColor('info')
                    ->badge()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('moduleChoice.'.ModuleChoice::titleAttribute())
                    ->label(__('admin.resources.practices.short_labels.module_choice'))
                    ->hidden(in_array('module_choice_id', $hiddenColumns, true))
                    ->icon(Heroicon::OutlinedSquares2x2)
                    ->iconColor('success')
                    ->badge()
                    ->sortable()
                    ->width('150px'),
                TextColumn::make('meditationType.'.MeditationType::titleAttribute())
                    ->label(__('admin.resources.practices.short_labels.meditation_type'))
                    ->hidden(in_array('meditation_type_id', $hiddenColumns, true))
                    ->icon(Heroicon::OutlinedSparkles)
                    ->iconColor('primary')
                    ->badge()
                    ->sortable()
                    ->width('120px'),
                TextColumn::make('duration')
                    ->label(__('admin.resources.practices.fields.duration'))
                    ->icon(Heroicon::OutlinedClock)
                    ->iconColor('gray')
                    ->formatStateUsing(fn (int $state): string => Practice::formatDuration($state))
                    ->sortable()
                    ->width('100px'),
                IconColumn::make('is_active')
                    ->label(__('admin.resources.practices.fields.is_active'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->width('80px'),
            ])
            ->filters(array_values(array_filter([
                static::dayFilter(
                    excluded: in_array('day', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'focus_problem_id',
                    label: __('admin.resources.practices.fields.focus_problem'),
                    relationship: 'focusProblem',
                    excluded: in_array('focus_problem_id', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'experience_level_id',
                    label: __('admin.resources.practices.fields.experience_level'),
                    relationship: 'experienceLevel',
                    excluded: in_array('experience_level_id', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'module_choice_id',
                    label: __('admin.resources.practices.fields.module_choice'),
                    relationship: 'moduleChoice',
                    excluded: in_array('module_choice_id', $excludedFilters, true),
                ),
                static::taxonomyFilter(
                    field: 'meditation_type_id',
                    label: __('admin.resources.practices.fields.meditation_type'),
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
            ->label(__('admin.resources.practices.fields.day'))
            ->native(false)
            ->searchable()
            ->preload()
            ->optionsLimit(29)
            ->indicateUsing(fn (array $state): ?string => static::dayFilterIndicator($state))
            ->options(fn (HasTable $livewire): array => static::dayOptionsWithCounts(
                static::tableFilters($livewire),
            ));
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
            ->native(false)
            ->relationship(
                $relationship,
                'id',
                fn (Builder $query, HasTable $livewire) => $query
                    ->forFilamentOptions()
                    ->withCount([
                        'practices' => fn (Builder $query) => static::applyPracticeFilters(
                            $query,
                            static::tableFilters($livewire),
                            excludedField: $field,
                        ),
                    ]),
            )
            ->getOptionLabelFromRecordUsing(
                fn ($record): string => Practice::formatCountedLabel(
                    $record->getTitle(app()->getLocale()),
                    (int) $record->practices_count,
                ),
            )
            ->indicateUsing(
                fn (array $state): ?string => Practice::relationFilterIndicator(
                    $field,
                    $state['value'] ?? null,
                    app()->getLocale(),
                    $label,
                ),
            )
            ->searchable()
            ->preload();
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private static function dayFilterIndicator(array $state): ?string
    {
        $value = $state['value'] ?? null;

        if (blank($value)) {
            return null;
        }

        return __('admin.resources.practices.filters.indicator', [
            'label' => __('admin.resources.practices.fields.day'),
            'value' => Practice::formatDay((int) $value),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private static function dayOptionsWithCounts(array $filters): array
    {
        $counts = static::applyPracticeFilters(
            Practice::query(),
            $filters,
            excludedField: 'day',
        )
            ->selectDayCounts()
            ->pluck('total', 'day');

        return collect(Practice::dayOptions())
            ->mapWithKeys(fn (string $label, int $day): array => [
                $day => Practice::formatCountedLabel($label, (int) ($counts[$day] ?? 0)),
            ])
            ->all();
    }

    /**
     * @param  Builder<Practice>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Practice>
     */
    private static function applyPracticeFilters(
        Builder $query,
        array $filters,
        ?string $excludedField = null,
    ): Builder {
        return $query
            ->forDay($excludedField === 'day' ? null : static::filterValue($filters, 'day'))
            ->forFocusProblem($excludedField === 'focus_problem_id' ? null : static::filterValue($filters, 'focus_problem_id'))
            ->forExperienceLevel($excludedField === 'experience_level_id' ? null : static::filterValue($filters, 'experience_level_id'))
            ->forModuleChoice($excludedField === 'module_choice_id' ? null : static::filterValue($filters, 'module_choice_id'))
            ->forMeditationType($excludedField === 'meditation_type_id' ? null : static::filterValue($filters, 'meditation_type_id'));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private static function filterValue(array $filters, string $field): ?int
    {
        $value = data_get($filters, "{$field}.value");

        return filled($value) ? (int) $value : null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function tableFilters(HasTable $livewire): array
    {
        return is_array($livewire->tableFilters ?? null) ? $livewire->tableFilters : [];
    }
}
