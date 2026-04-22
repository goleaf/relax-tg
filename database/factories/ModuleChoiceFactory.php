<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleChoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => [
                'en' => $this->faker->unique()->word(),
                'ru' => $this->faker->word(),
            ],
        ];
    }
}
