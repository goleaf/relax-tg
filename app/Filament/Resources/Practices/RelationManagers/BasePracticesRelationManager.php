<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use App\Filament\Resources\Practices\Schemas\PracticeForm;
use App\Filament\Resources\Practices\Tables\PracticesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

abstract class BasePracticesRelationManager extends RelationManager
{
    protected static string $relationship = 'practices';

    protected static bool $isLazy = false;

    protected static ?string $title = 'Practices';

    abstract protected static function getOwnerForeignKey(): string;

    public function form(Schema $schema): Schema
    {
        return PracticeForm::configure($schema, hiddenFields: [
            static::getOwnerForeignKey(),
        ]);
    }

    public function table(Table $table): Table
    {
        return PracticesTable::configure(
            $table,
            hiddenColumns: [static::getOwnerForeignKey()],
            excludedFilters: [static::getOwnerForeignKey()],
            headerActions: [
                CreateAction::make(),
            ],
        );
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->practices()->count();

        return (string) $count;
    }
}
