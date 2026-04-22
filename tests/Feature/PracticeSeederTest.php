<?php

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\PracticeSeeder;

test('practice seeder creates every category combination for each day', function () {
    $this->seed([
        LanguageSeeder::class,
        PracticeSeeder::class,
    ]);

    expect(FocusProblem::query()->count())->toBe(5)
        ->and(ExperienceLevel::query()->count())->toBe(3)
        ->and(ModuleChoice::query()->count())->toBe(3)
        ->and(MeditationType::query()->count())->toBe(6)
        ->and(Practice::query()->count())->toBe(29 * 5 * 3 * 3 * 6)
        ->and(Practice::query()->where('day', 1)->count())->toBe(5 * 3 * 3 * 6)
        ->and(Practice::query()->where('day', 29)->count())->toBe(5 * 3 * 3 * 6);
});

test('practice seeder creates translated day content for every day', function () {
    $this->seed([
        LanguageSeeder::class,
        PracticeSeeder::class,
    ]);

    $firstPractice = Practice::query()
        ->where('day', 1)
        ->firstOrFail();

    $lastPractice = Practice::query()
        ->where('day', 29)
        ->firstOrFail();

    expect(Language::enabled()->pluck('code')->sort()->values()->all())->toBe(['en', 'ru'])
        ->and($firstPractice->title['en'])->toStartWith('Day 1: Arrive With One Breath')
        ->and($firstPractice->title['ru'])->toStartWith('День 1: Вернуться одним дыханием')
        ->and($firstPractice->description['en'])->toContain('Best for')
        ->and($firstPractice->description['ru'])->toContain('Подходит для темы')
        ->and($lastPractice->title['en'])->toStartWith('Day 29: Build Your Ongoing Practice')
        ->and($lastPractice->title['ru'])->toStartWith('День 29: Собрать свою постоянную практику')
        ->and($lastPractice->description['en'])->toContain('consistently')
        ->and($lastPractice->description['ru'])->toContain('регулярно');
});
