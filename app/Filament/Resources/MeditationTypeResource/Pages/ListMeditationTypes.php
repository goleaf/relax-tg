<?php

namespace App\Filament\Resources\MeditationTypeResource\Pages;

use App\Filament\Resources\MeditationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeditationTypes extends ListRecords
{
    protected static string $resource = MeditationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
