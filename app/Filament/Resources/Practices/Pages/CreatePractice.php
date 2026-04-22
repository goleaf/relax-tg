<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePractice extends CreateRecord
{
    protected static string $resource = PracticeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $queryParams = [
            'day',
            'focus_problem',
            'experience_level',
            'module_choice',
            'meditation_type',
        ];

        foreach ($queryParams as $param) {
            if ($value = request()->query($param)) {
                $data[$param] = $value;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', [
            'day' => $this->record->day,
            'focus_problem' => $this->record->focus_problem->value ?? $this->record->focus_problem,
            'experience_level' => $this->record->experience_level->value ?? $this->record->experience_level,
            'module_choice' => $this->record->module_choice->value ?? $this->record->module_choice,
            'meditation_type' => $this->record->meditation_type->value ?? $this->record->meditation_type,
        ]);
    }
}
