<?php

namespace Database\Factories;

use App\Enums\ExperienceLevel;
use App\Enums\FocusProblem;
use App\Enums\MeditationType;
use App\Enums\ModuleChoice;
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
            'day' => $this->faker->numberBetween(1, 29),
            'focus_problem' => $this->faker->randomElement(FocusProblem::cases()),
            'experience_level' => $this->faker->randomElement(ExperienceLevel::cases()),
            'module_choice' => $this->faker->randomElement(ModuleChoice::cases()),
            'meditation_type' => $this->faker->randomElement(MeditationType::cases()),
            'duration' => $this->faker->numberBetween(60, 1800), // 1 to 30 minutes
            'image_url' => $this->faker->imageUrl(),
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_active' => $this->faker->boolean(90),
            'title' => $title,
            'description' => $description,
        ];
    }
}
