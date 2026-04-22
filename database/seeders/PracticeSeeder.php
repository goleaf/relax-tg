<?php

namespace Database\Seeders;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories
        $focusProblems = [
            ['title' => ['en' => 'Anxiety', 'ru' => 'Тревога']],
            ['title' => ['en' => 'Fatigue', 'ru' => 'Усталость']],
            ['title' => ['en' => 'Focus', 'ru' => 'Фокус']],
            ['title' => ['en' => 'Anger', 'ru' => 'Гнев']],
            ['title' => ['en' => 'Autopilot', 'ru' => 'Автопилот']],
        ];
        foreach ($focusProblems as $item) {
            FocusProblem::create($item);
        }

        $experienceLevels = [
            ['title' => ['en' => 'Beginner', 'ru' => 'Новичок']],
            ['title' => ['en' => 'Intermediate', 'ru' => 'Средний']],
            ['title' => ['en' => 'Advanced', 'ru' => 'Продвинутый']],
        ];
        foreach ($experienceLevels as $item) {
            ExperienceLevel::create($item);
        }

        $moduleChoices = [
            ['title' => ['en' => 'Main', 'ru' => 'Главный']],
            ['title' => ['en' => 'Nutrition', 'ru' => 'Питание']],
            ['title' => ['en' => 'All', 'ru' => 'Все']],
        ];
        foreach ($moduleChoices as $item) {
            ModuleChoice::create($item);
        }

        $meditationTypes = [
            ['title' => ['en' => 'Breath', 'ru' => 'Дыхание']],
            ['title' => ['en' => 'Body', 'ru' => 'Тело']],
            ['title' => ['en' => 'Observation', 'ru' => 'Наблюдение']],
            ['title' => ['en' => 'Movement', 'ru' => 'Движение']],
            ['title' => ['en' => 'Pause', 'ru' => 'Пауза']],
            ['title' => ['en' => 'Space', 'ru' => 'Пространство']],
        ];
        foreach ($meditationTypes as $item) {
            MeditationType::create($item);
        }

        // 2. Seed Practices
        $fpIds = FocusProblem::pluck('id')->toArray();
        $elIds = ExperienceLevel::pluck('id')->toArray();
        $mcIds = ModuleChoice::pluck('id')->toArray();
        $mtIds = MeditationType::pluck('id')->toArray();

        for ($day = 1; $day <= 29; $day++) {
            Practice::factory(2)->create([
                'day' => $day,
                'focus_problem_id' => fn () => fake()->randomElement($fpIds),
                'experience_level_id' => fn () => fake()->randomElement($elIds),
                'module_choice_id' => fn () => fake()->randomElement($mcIds),
                'meditation_type_id' => fn () => fake()->randomElement($mtIds),
            ]);
        }
    }
}
