<?php

namespace App\Filament\Resources\FocusProblemResource\Pages;

use App\Filament\Resources\FocusProblemResource;
use App\Models\FocusProblem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFocusProblem extends EditRecord
{
    protected static string $resource = FocusProblemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn (FocusProblem $record): bool => $record->isInUse()),
        ];
    }
}
