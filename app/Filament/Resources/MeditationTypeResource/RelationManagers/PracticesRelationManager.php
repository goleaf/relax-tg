<?php

namespace App\Filament\Resources\MeditationTypeResource\RelationManagers;

use App\Filament\Resources\Practices\RelationManagers\BasePracticesRelationManager;

class PracticesRelationManager extends BasePracticesRelationManager
{
    protected static function getOwnerForeignKey(): string
    {
        return 'meditation_type_id';
    }
}
