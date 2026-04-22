<?php

namespace App\Filament\Resources\FocusProblemResource\RelationManagers;

use App\Filament\Resources\Practices\RelationManagers\BasePracticesRelationManager;

class PracticesRelationManager extends BasePracticesRelationManager
{
    protected static function getOwnerForeignKey(): string
    {
        return 'focus_problem_id';
    }
}
