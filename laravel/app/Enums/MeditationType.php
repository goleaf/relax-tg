<?php

namespace App\Enums;

enum MeditationType: string
{
    case Breath = 'breath';
    case Body = 'body';
    case Observation = 'observation';
    case Movement = 'movement';
    case Pause = 'pause';
    case Space = 'space';

    public function label(): string
    {
        return __("enums.meditation_type.{$this->value}");
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
