<?php

namespace App\Filament\Resources\Languages\Pages;

use App\Filament\Resources\Languages\LanguageResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLanguages extends ListRecords
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('admin.resources.languages.tabs.all')),
            'enabled' => Tab::make(__('admin.resources.languages.tabs.enabled'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_enabled', true)),
            'disabled' => Tab::make(__('admin.resources.languages.tabs.disabled'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_enabled', false)),
        ];
    }
}
