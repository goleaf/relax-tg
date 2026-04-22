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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PracticeResource extends Resource
{
    protected static ?string $model = Practice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function getNavigationItems(): array
    {
        $items = [];

        $counts = Practice::select('day', DB::raw('count(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        for ($i = 1; $i <= 29; $i++) {
            $count = $counts[$i] ?? 0;
            $items[] = NavigationItem::make("{$i} Day ({$count})")
                ->group('Daily Practices')
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.practices.index') && request()->query('day') == $i)
                ->url(static::getUrl('index', ['day' => $i]));
        }

        return $items;
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
