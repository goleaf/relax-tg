<?php

namespace App\Filament\Widgets;

use App\Filament\Support\PracticeDashboardData;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class PracticeDurationChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    protected ?string $pollingInterval = null;

    protected PracticeDashboardData $dashboardData;

    public function boot(PracticeDashboardData $dashboardData): void
    {
        $this->dashboardData = $dashboardData;
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => __('admin.widgets.practice_duration.dataset'),
                    'data' => $this->dashboardData->averageDurationMinutesByDay(),
                    'backgroundColor' => 'rgba(15, 118, 110, 0.78)',
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $this->dashboardData->dayLabels(),
        ];
    }

    public function getDescription(): ?string
    {
        return __('admin.widgets.practice_duration.description');
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.widgets.practice_duration.heading');
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
