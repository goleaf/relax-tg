<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\MeditationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeditationType>
 */
class MeditationTypeFactory extends Factory
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
