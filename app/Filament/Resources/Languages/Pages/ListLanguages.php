<?php

namespace App\Filament\Resources\Languages\Pages;

use App\Filament\Resources\Languages\LanguageResource;
use Filament\Resources\Pages\ListRecords;
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
            'all' => \Filament\Schemas\Components\Tabs\Tab::make('All Languages'),
            'enabled' => \Filament\Schemas\Components\Tabs\Tab::make('Enabled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_enabled', true)),
            'disabled' => \Filament\Schemas\Components\Tabs\Tab::make('Disabled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_enabled', false)),
        ];
    }
}
