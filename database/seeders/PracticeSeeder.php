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
            ['slug' => 'anxiety', 'title' => ['en' => 'Anxiety', 'ru' => 'Тревога']],
            ['slug' => 'fatigue', 'title' => ['en' => 'Fatigue', 'ru' => 'Усталость']],
            ['slug' => 'focus', 'title' => ['en' => 'Focus', 'ru' => 'Фокус']],
            ['slug' => 'anger', 'title' => ['en' => 'Anger', 'ru' => 'Гнев']],
            ['slug' => 'autopilot', 'title' => ['en' => 'Autopilot', 'ru' => 'Автопилот']],
        ];
        foreach ($focusProblems as $item) {
            FocusProblem::updateOrCreate(['slug' => $item['slug']], $item);
        }

        $experienceLevels = [
            ['slug' => 'beginner', 'title' => ['en' => 'Beginner', 'ru' => 'Новичок']],
            ['slug' => 'intermediate', 'title' => ['en' => 'Intermediate', 'ru' => 'Средний']],
            ['slug' => 'advanced', 'title' => ['en' => 'Advanced', 'ru' => 'Продвинутый']],
        ];
        foreach ($experienceLevels as $item) {
            ExperienceLevel::updateOrCreate(['slug' => $item['slug']], $item);
        }

        $moduleChoices = [
            ['slug' => 'main', 'title' => ['en' => 'Main', 'ru' => 'Главный']],
            ['slug' => 'nutrition', 'title' => ['en' => 'Nutrition', 'ru' => 'Питание']],
            ['slug' => 'all', 'title' => ['en' => 'All', 'ru' => 'Все']],
        ];
        foreach ($moduleChoices as $item) {
            ModuleChoice::updateOrCreate(['slug' => $item['slug']], $item);
        }

        $meditationTypes = [
            ['slug' => 'breath', 'title' => ['en' => 'Breath', 'ru' => 'Дыхание']],
            ['slug' => 'body', 'title' => ['en' => 'Body', 'ru' => 'Тело']],
            ['slug' => 'observation', 'title' => ['en' => 'Observation', 'ru' => 'Наблюдение']],
            ['slug' => 'movement', 'title' => ['en' => 'Movement', 'ru' => 'Движение']],
            ['slug' => 'pause', 'title' => ['en' => 'Pause', 'ru' => 'Пауза']],
            ['slug' => 'space', 'title' => ['en' => 'Space', 'ru' => 'Пространство']],
        ];
        foreach ($meditationTypes as $item) {
            MeditationType::updateOrCreate(['slug' => $item['slug']], $item);
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
