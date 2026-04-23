<?php

namespace App\Filament\Resources\ExperienceLevelResource\Pages;

use App\Filament\Resources\ExperienceLevelResource;
use App\Models\ExperienceLevel;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExperienceLevel extends EditRecord
{
    protected static string $resource = ExperienceLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn (ExperienceLevel $record): bool => $record->isInUse()),
        ];
    }
}
