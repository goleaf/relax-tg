<?php

namespace App\Filament\Support;

use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PracticeDashboardData
{
    /**
     * @var EloquentCollection<int, FocusProblem>|null
     */
    private ?EloquentCollection $focusProblems = null;

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

    public function averageDurationMinutesByDay(): array
    {
        return $this->seriesForDays(
            fn (int $day): float => $this->durationAveragesByDay()[$day] ?? 0.0,
        );
    }

    public function averageDurationSeconds(): int
    {
        return $this->averageDurationSeconds ??= (int) round((float) (Practice::query()->avg('duration') ?? 0));
    }

    public function dayCoverageSeries(): array
    {
        return $this->seriesForDays(
            fn (int $day): int => (($this->dayCounts()[$day] ?? 0) > 0) ? 1 : 0,
        );
    }

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
        return $this->enabledLanguages ??= Language::query()
            ->enabled()
            ->count();
    }

    /**
     * @return array<int, array{count: int, label: string}>
     */
    public function focusProblemDistribution(): array
    {
        return $this->focusProblemDistribution ??= $this->focusProblems()
            ->map(function (FocusProblem $focusProblem): array {
                return [
                    'label' => $focusProblem->getTitle(app()->getLocale()),
                    'count' => (int) $focusProblem->practices_count,
                ];
            })
            ->filter(fn (array $segment): bool => $segment['count'] > 0)
            ->values()
            ->all();
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
        return $this->mediaReadyPractices ??= Practice::query()
            ->whereNotNull('image_path')
            ->whereNotNull('video_path')
            ->count();
    }

    public function mediaReadyPracticesByDay(): array
    {
        return $this->seriesForDays(
            fn (int $day): int => $this->mediaReadyCountsByDay()[$day] ?? 0,
        );
    }

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

    private function focusProblems(): EloquentCollection
    {
        return $this->focusProblems ??= FocusProblem::query()
            ->forFilamentOptions()
            ->withCount('practices')
            ->get();
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
     * @param  callable(int): float|int  $callback
     * @return array<int, float|int>
     */
    private function seriesForDays(callable $callback): array
    {
        return collect(array_keys(Practice::dayOptions()))
            ->map(function (int $day) use ($callback): float|int {
                return $callback($day);
            })
            ->all();
    }
}
