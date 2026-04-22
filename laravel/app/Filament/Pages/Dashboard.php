<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FocusProblemDistributionChart;
use App\Filament\Widgets\PracticeDurationChart;
use App\Filament\Widgets\PracticeOverviewStats;
use App\Filament\Widgets\PracticeVolumeChart;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;

class Dashboard extends BaseDashboard
{
    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            PracticeOverviewStats::class,
            PracticeVolumeChart::class,
            PracticeDurationChart::class,
            FocusProblemDistributionChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return collect(parent::getNavigationItems())
            ->map(fn (NavigationItem $item): NavigationItem => $item->extraAttributes([
                'data-test-topbar-link' => 'dashboard',
            ]))
            ->all();
    }
}
