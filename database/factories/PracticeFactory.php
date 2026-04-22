<?php

namespace Database\Factories;

use App\Models\Language;
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
        $codes = once(fn () => Language::where('is_enabled', true)->pluck('code')->toArray());

        if (empty($codes)) {
            $codes = ['en'];
        }

        $title = [];
        $description = [];

        foreach ($codes as $code) {
            $title[$code] = $this->faker->sentence(4, false);
            $description[$code] = $this->faker->paragraphs(2, true);
        }

        return [
            'title' => $title,
            'description' => $description,
        ];
    }
}
