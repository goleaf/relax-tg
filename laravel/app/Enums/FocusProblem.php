<?php

namespace App\Enums;

enum FocusProblem: string
{
    case Anxiety = 'anxiety';
    case Fatigue = 'fatigue';
    case Focus = 'focus';
    case Anger = 'anger';
    case Autopilot = 'autopilot';

    public function label(): string
    {
        return __("enums.focus_problem.{$this->value}");
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case): string => $case->value, self::cases());
    }
}
