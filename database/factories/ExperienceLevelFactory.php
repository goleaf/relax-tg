<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceLevelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'title' => [
                'en' => $this->faker->word(),
                'ru' => $this->faker->word(),
            ],
            'is_enabled' => true,
        ];
    }
}
