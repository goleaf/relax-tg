<?php

namespace App\Filament\Widgets;

use App\Filament\Support\PracticeDashboardData;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class PracticeVolumeChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

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
                    'label' => __('admin.widgets.practice_volume.dataset'),
                    'data' => $this->dashboardData->practiceCountsByDay(),
                    'backgroundColor' => 'rgba(217, 119, 6, 0.14)',
                    'borderColor' => '#d97706',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $this->dashboardData->dayLabels(),
        ];
    }

    public function getDescription(): ?string
    {
        return __('admin.widgets.practice_volume.description');
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.widgets.practice_volume.heading');
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
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
