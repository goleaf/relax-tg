<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePractice extends CreateRecord
{
    protected static string $resource = PracticeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($day = request()->query('day')) {
            $data['day'] = $day;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['day' => $this->record->day]);
    }
}
