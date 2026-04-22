<?php

namespace App\Filament\Resources\ModuleChoiceResource\RelationManagers;

use App\Filament\Resources\Practices\RelationManagers\BasePracticesRelationManager;

class PracticesRelationManager extends BasePracticesRelationManager
{
    protected static function getOwnerForeignKey(): string
    {
        return 'module_choice_id';
    }
}
