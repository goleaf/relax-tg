<?php

namespace App\Filament\Widgets;

use App\Filament\Support\PracticeDashboardData;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class FocusProblemDistributionChart extends ChartWidget
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
        $segments = $this->dashboardData->focusProblemDistribution();

        if ($segments === []) {
            $segments = [
                [
                    'label' => __('admin.widgets.focus_problem_distribution.empty'),
                    'count' => 1,
                ],
            ];
        }

        $counts = array_column($segments, 'count');
        $labels = array_column($segments, 'label');

        return [
            'datasets' => [
                [
                    'label' => __('admin.widgets.focus_problem_distribution.dataset'),
                    'data' => $counts,
                    'backgroundColor' => [
                        '#d97706',
                        '#0891b2',
                        '#059669',
                        '#7c3aed',
                        '#ef4444',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getDescription(): ?string
    {
        return __('admin.widgets.focus_problem_distribution.description');
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.widgets.focus_problem_distribution.heading');
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
