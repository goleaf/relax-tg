<?php

namespace App\Filament\Resources\ModuleChoiceResource\Pages;

use App\Filament\Resources\ModuleChoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModuleChoices extends ListRecords
{
    protected static string $resource = ModuleChoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
