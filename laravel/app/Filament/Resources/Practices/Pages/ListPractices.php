<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use App\Models\Practice;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPractices extends ListRecords
{
    protected static string $resource = PracticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(fn () => PracticeResource::getUrl('create', [
                    'filters' => $this->tableFilters,
                ])),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        $filteredTableQuery = $this->getFilteredTableQuery();

        if ($filteredTableQuery === null) {
            return parent::getTitle();
        }

        $count = (clone $filteredTableQuery)->count();

        return Practice::getListTitle($this->tableFilters ?? [], app()->getLocale(), $count);
    }
}
