<?php

namespace App\Filament\Resources\MeditationTypeResource\Pages;

use App\Filament\Resources\MeditationTypeResource;
use App\Models\MeditationType;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeditationType extends EditRecord
{
    protected static string $resource = MeditationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn (MeditationType $record): bool => $record->isInUse()),
        ];
    }
}
