<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use App\Filament\Resources\Practices\Schemas\PracticeForm;
use App\Filament\Resources\Practices\Tables\PracticesTable;
use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

abstract class BasePracticesRelationManager extends RelationManager
{
    protected static string $relationship = 'practices';

    protected static bool $isLazy = false;

    abstract protected static function getOwnerForeignKey(): string;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.relation_managers.practices');
    }

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
        if (! $ownerRecord instanceof FocusProblem
            && ! $ownerRecord instanceof ExperienceLevel
            && ! $ownerRecord instanceof ModuleChoice
            && ! $ownerRecord instanceof MeditationType) {
            return null;
        }

        if ($ownerRecord->practices_count === null) {
            $ownerRecord->loadCount('practices');
        }

        $count = $ownerRecord->practices_count ?? 0;

        return (string) $count;
    }
}
