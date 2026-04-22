<?php

namespace Database\Factories;

use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Practice>
 */
class PracticeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languages = \App\Models\Language::where('is_enabled', true)->pluck('code')->toArray();
        if (empty($languages)) {
            $languages = ['en'];
        }

        $title = [];
        $description = [];

        foreach ($languages as $code) {
            $title[$code] = $this->faker->words(3, true) . " ({$code})";
            $description[$code] = $this->faker->paragraph(3) . " ({$code})";
        }

        return [
            'title' => $title,
            'description' => $description,
        ];
    }
}
