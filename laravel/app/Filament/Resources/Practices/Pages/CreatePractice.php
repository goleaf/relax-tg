<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use App\Models\Practice;
use Filament\Resources\Pages\CreateRecord;

class CreatePractice extends CreateRecord
{
    protected static string $resource = PracticeResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
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
        $record = $this->getRecord();

        if (! $record instanceof Practice) {
            return PracticeResource::getUrl('index');
        }

        $filters = [];

        foreach ([
            'day' => $record->day,
            'focus_problem_id' => $record->focus_problem_id,
            'experience_level_id' => $record->experience_level_id,
            'module_choice_id' => $record->module_choice_id,
            'meditation_type_id' => $record->meditation_type_id,
        ] as $field => $value) {
            if ($value !== null) {
                $filters[$field] = ['value' => $value];
            }
        }

        return PracticeResource::getUrl('index', [
            'filters' => $filters,
        ]);
    }
}
