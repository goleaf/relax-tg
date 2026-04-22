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
                ->url(fn () => PracticeResource::getUrl('create', ['day' => request()->query('day')])),
        ];
    }

    protected function applyFiltersToTableQuery(Builder $query, bool $isResolvingRecord = false): Builder
    {
        if ($day = request()->query('day')) {
            $query->where('day', $day);
        }

        return $query;
    }
}
