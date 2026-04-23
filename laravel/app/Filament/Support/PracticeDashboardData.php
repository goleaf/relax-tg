<?php

namespace App\Filament\Support;

use App\Models\Language;
use App\Models\Practice;
use Closure;

class PracticeDashboardData
{
    /**
     * @var array<int, int>|null
     */
    private ?array $dayCounts = null;

    /**
     * @var array<int, float>|null
     */
    private ?array $durationAveragesByDay = null;

    /**
     * @var array<int, array{count: int, label: string}>|null
     */
    private ?array $focusProblemDistribution = null;

    private ?int $enabledLanguages = null;

    /**
     * @var array<int, int>|null
     */
    private ?array $mediaReadyCountsByDay = null;

    private ?int $averageDurationSeconds = null;

    private ?int $mediaReadyPractices = null;

    /**
     * @return array<int, float|int>
     */
    public function averageDurationMinutesByDay(): array
    {
        return $this->seriesForDays(
            fn (int $day): float => $this->durationAveragesByDay()[$day] ?? 0.0,
        );
    }

    public function averageDurationSeconds(): int
    {
        return $this->averageDurationSeconds ??= Practice::averageDurationSeconds();
    }

    /**
     * @return array<int, float|int>
     */
    public function dayCoverageSeries(): array
    {
        return $this->seriesForDays(
            fn (int $day): int => (($this->dayCounts()[$day] ?? 0) > 0) ? 1 : 0,
        );
    }

    /**
     * @return array<int, string>
     */
    public function dayLabels(): array
    {
        return array_values(Practice::dayOptions());
    }

    public function daysCovered(): int
    {
        return collect($this->dayCounts())
            ->filter(fn (int $count): bool => $count > 0)
            ->count();
    }

    public function enabledLanguages(): int
    {
        return $this->enabledLanguages ??= Language::enabledCount();
    }

    /**
     * @return array<int, array{count: int, label: string}>
     */
    public function focusProblemDistribution(): array
    {
        return $this->focusProblemDistribution ??= Practice::focusProblemDistribution(app()->getLocale());
    }

    public function formattedAverageDuration(): string
    {
        return Practice::formatDuration($this->averageDurationSeconds());
    }

    public function mediaReadyPercentage(): int
    {
        $totalPractices = $this->totalPractices();

        if ($totalPractices === 0) {
            return 0;
        }

        return (int) round(($this->mediaReadyPractices() / $totalPractices) * 100);
    }

    public function mediaReadyPractices(): int
    {
        return $this->mediaReadyPractices ??= Practice::mediaReadyPracticeCount();
    }

    /**
     * @return array<int, float|int>
     */
    public function mediaReadyPracticesByDay(): array
    {
        return $this->seriesForDays(
            fn (int $day): int => $this->mediaReadyCountsByDay()[$day] ?? 0,
        );
    }

    /**
     * @return array<int, float|int>
     */
    public function practiceCountsByDay(): array
    {
        return $this->seriesForDays(
            fn (int $day): int => $this->dayCounts()[$day] ?? 0,
        );
    }

    public function totalPractices(): int
    {
        return array_sum($this->dayCounts());
    }

    /**
     * @return array<int, int>
     */
    private function dayCounts(): array
    {
        return $this->dayCounts ??= Practice::dayCounts();
    }

    /**
     * @return array<int, float>
     */
    private function durationAveragesByDay(): array
    {
        return $this->durationAveragesByDay ??= Practice::averageDurationMinutesByDay();
    }

    /**
     * @return array<int, int>
     */
    private function mediaReadyCountsByDay(): array
    {
        return $this->mediaReadyCountsByDay ??= Practice::mediaReadyCountsByDay();
    }

    /**
     * @param  Closure(int): (float|int)  $callback
     * @return array<int, float|int>
     */
    private function seriesForDays(Closure $callback): array
    {
        return collect(array_keys(Practice::dayOptions()))
            ->map(function (int $day) use ($callback): float|int {
                return $callback($day);
            })
            ->all();
    }
}
