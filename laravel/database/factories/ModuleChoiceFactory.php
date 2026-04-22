<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\ModuleChoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModuleChoice>
 */
class ModuleChoiceFactory extends Factory
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
