<?php

namespace App\Filament\Resources\ExperienceLevelResource\RelationManagers;

use App\Filament\Resources\Practices\RelationManagers\BasePracticesRelationManager;

class PracticesRelationManager extends BasePracticesRelationManager
{
    protected static function getOwnerForeignKey(): string
    {
        return 'experience_level_id';
    }
}
