<?php

namespace Database\Factories;

use App\Models\ExperienceLevel;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExperienceLevel>
 */
class ExperienceLevelFactory extends Factory
{
    public function definition(): array
    {
        $codes = Language::enabledCodes();

        if ($codes === []) {
            $codes = ['en'];
        }

        $title = [];

        foreach ($codes as $code) {
            $title[$code] = $this->faker->words(2, true);
        }

        return [
            'title' => $title,
        ];
    }
}
