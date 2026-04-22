<?php

namespace Database\Factories;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
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
            'day' => $this->faker->numberBetween(1, 29),
            'focus_problem_id' => FocusProblem::inRandomOrder()->first()?->id ?? FocusProblem::factory(),
            'experience_level_id' => ExperienceLevel::inRandomOrder()->first()?->id ?? ExperienceLevel::factory(),
            'module_choice_id' => ModuleChoice::inRandomOrder()->first()?->id ?? ModuleChoice::factory(),
            'meditation_type_id' => MeditationType::inRandomOrder()->first()?->id ?? MeditationType::factory(),
            'duration' => $this->faker->numberBetween(60, 1800),
            'image_url' => $this->faker->imageUrl(),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_active' => $this->faker->boolean(90),
            'title' => $title,
            'description' => $description,
        ];
    }
}
