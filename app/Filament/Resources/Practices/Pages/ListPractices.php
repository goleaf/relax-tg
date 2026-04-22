<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPractices extends ListRecords
{
    protected static string $resource = PracticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(fn () => PracticeResource::getUrl('create', [
                    'day' => request()->query('day'),
                    'focus_problem' => request()->query('focus_problem'),
                    'experience_level' => request()->query('experience_level'),
                    'module_choice' => request()->query('module_choice'),
                    'meditation_type' => request()->query('meditation_type'),
                ])),
        ];
    }

    protected function applyFiltersToTableQuery(Builder $query, bool $isResolvingRecord = false): Builder
    {
        if ($day = request()->query('day')) {
            $query->where('day', $day);
        }

        if ($focusProblem = request()->query('focus_problem')) {
            $query->where('focus_problem', $focusProblem);
        }

        if ($experienceLevel = request()->query('experience_level')) {
            $query->where('experience_level', $experienceLevel);
        }

        if ($moduleChoice = request()->query('module_choice')) {
            $query->where('module_choice', $moduleChoice);
        }

        if ($meditationType = request()->query('meditation_type')) {
            $query->where('meditation_type', $meditationType);
        }

        return $query;
    }
}
