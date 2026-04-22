<?php

namespace App\Filament\Widgets;

use App\Filament\Support\PracticeDashboardData;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PracticeOverviewStats extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected PracticeDashboardData $dashboardData;

    public function boot(PracticeDashboardData $dashboardData): void
    {
        $this->dashboardData = $dashboardData;
    }

    protected function getDescription(): ?string
    {
        return __('admin.widgets.practice_overview.description');
    }

    protected function getHeading(): ?string
    {
        return __('admin.widgets.practice_overview.heading');
    }

    protected function getStats(): array
    {
        $totalPractices = $this->dashboardData->totalPractices();
        $totalDays = count($this->dashboardData->dayLabels());
        $daysCovered = $this->dashboardData->daysCovered();
        $mediaReadyPractices = $this->dashboardData->mediaReadyPractices();

        return [
            Stat::make(__('admin.widgets.practice_overview.stats.total_practices.label'), number_format($totalPractices))
                ->description(__('admin.widgets.practice_overview.stats.total_practices.description'))
                ->chart($this->dashboardData->practiceCountsByDay())
                ->color('warning'),
            Stat::make(__('admin.widgets.practice_overview.stats.enabled_languages.label'), number_format($this->dashboardData->enabledLanguages()))
                ->description(__('admin.widgets.practice_overview.stats.enabled_languages.description'))
                ->color('primary'),
            Stat::make(__('admin.widgets.practice_overview.stats.days_covered.label'), $daysCovered.'/'.$totalDays)
                ->description(__('admin.widgets.practice_overview.stats.days_covered.description'))
                ->chart($this->dashboardData->dayCoverageSeries())
                ->color($daysCovered === $totalDays ? 'success' : 'warning'),
            Stat::make(__('admin.widgets.practice_overview.stats.media_ready.label'), number_format($mediaReadyPractices))
                ->description(__('admin.widgets.practice_overview.stats.media_ready.description', [
                    'percent' => $this->dashboardData->mediaReadyPercentage(),
                ]))
                ->chart($this->dashboardData->mediaReadyPracticesByDay())
                ->color('success'),
            Stat::make(__('admin.widgets.practice_overview.stats.average_session.label'), $this->dashboardData->formattedAverageDuration())
                ->description(__('admin.widgets.practice_overview.stats.average_session.description'))
                ->chart($this->dashboardData->averageDurationMinutesByDay())
                ->color('info'),
        ];
    }
}
