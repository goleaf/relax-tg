<?php

namespace App\Enums;

enum ExperienceLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    public function label(): string
    {
        return __("enums.experience_level.{$this->value}");
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public static function values(): array
    {
        return array_map(fn (self $case): string => $case->value, self::cases());
    }
}
