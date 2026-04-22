<?php

namespace App\Filament\Resources\Practices;

use App\Filament\Resources\Practices\Pages\CreatePractice;
use App\Filament\Resources\Practices\Pages\EditPractice;
use App\Filament\Resources\Practices\Pages\ListPractices;
use App\Filament\Resources\Practices\Schemas\PracticeForm;
use App\Filament\Resources\Practices\Tables\PracticesTable;
use App\Models\Practice;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PracticeResource extends Resource
{
    protected static ?string $model = Practice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function getNavigationItems(): array
    {
        return collect(Practice::getNavigationTree())
            ->map(function (array $dayData): NavigationItem {
                $day = $dayData['day'];

                return NavigationItem::make(Practice::formatDay($day)." ({$dayData['count']})")
                    ->group('Daily Practices')
                    ->icon(static::getNavigationIcon())
                    ->sort($day)
                    ->isActiveWhen(fn (): bool => static::isDayNavigationItemActive($day))
                    ->url(static::getNavigationUrlForDay($day));
            })
            ->all();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->forResourceIndex();
    }

    private static function getNavigationUrlForDay(int $day): string
    {
        return static::getUrl('index', [
            'filters' => [
                'day' => ['value' => $day],
            ],
        ]);
    }

    private static function isDayNavigationItemActive(int $day): bool
    {
        return (int) data_get(request()->query('filters', []), 'day.value') === $day;
    }

    /** @param Practice|null $record */
    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if ($record === null) {
            return static::getModelLabel();
        }

        return $record->getTitle(app()->getLocale());
    }

    public static function form(Schema $schema): Schema
    {
        return PracticeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPractices::route('/'),
            'create' => CreatePractice::route('/create'),
            'edit' => EditPractice::route('/{record}/edit'),
        ];
    }
}
