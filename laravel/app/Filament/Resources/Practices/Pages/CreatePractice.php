<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePractice extends CreateRecord
{
    protected static string $resource = PracticeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $filterFields = [
            'day',
            'focus_problem_id',
            'experience_level_id',
            'module_choice_id',
            'meditation_type_id',
        ];

        foreach ($filterFields as $field) {
            $value = data_get(request()->query('filters', []), "{$field}.value");

            if (filled($value)) {
                $data[$field] = $value;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $filters = collect([
            'day' => $this->record->day,
            'focus_problem_id' => $this->record->focus_problem_id,
            'experience_level_id' => $this->record->experience_level_id,
            'module_choice_id' => $this->record->module_choice_id,
            'meditation_type_id' => $this->record->meditation_type_id,
        ])
            ->filter(fn (?int $value): bool => filled($value))
            ->map(fn (int $value): array => ['value' => $value])
            ->all();

        return $this->getResource()::getUrl('index', [
            'filters' => $filters,
        ]);
    }
}
