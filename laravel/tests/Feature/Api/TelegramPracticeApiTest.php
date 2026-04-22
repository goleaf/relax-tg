<?php

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;

beforeEach(function () {
    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);

    config()->set('services.telegram.internal_api_token', 'telegram-api-token');
});

test('telegram practices api requires a valid bearer token', function () {
    $this->getJson('/api/telegram/practices')
        ->assertUnauthorized()
        ->assertJson([
            'message' => __('http-statuses.401'),
        ]);
});

test('telegram practices api reports missing token configuration with a translated message', function () {
    config()->set('services.telegram.internal_api_token', '');

    $this->getJson('/api/telegram/practices')
        ->assertStatus(503)
        ->assertJson([
            'message' => __('telegram.api.token_not_configured'),
        ]);
});

test('telegram practices api returns localized practice data from filament managed content', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create([
        'title' => ['en' => 'Beginner', 'ru' => 'Начальный'],
    ]);
    $moduleChoice = ModuleChoice::factory()->create([
        'title' => ['en' => 'Breathing', 'ru' => 'Дыхание'],
    ]);
    $meditationType = MeditationType::factory()->create([
        'title' => ['en' => 'Guided', 'ru' => 'С сопровождением'],
    ]);

    Practice::factory()->create([
        'day' => 5,
        'duration' => 600,
        'is_active' => false,
        'title' => ['en' => 'Inactive practice', 'ru' => 'Неактивная практика'],
    ]);

    $practice = Practice::factory()->create([
        'day' => 5,
        'duration' => 780,
        'is_active' => true,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
        'title' => ['en' => 'Body scan', 'ru' => 'Сканирование тела'],
        'description' => ['en' => 'Relax the whole body.', 'ru' => 'Расслабьте все тело.'],
    ]);

    $response = $this->withToken('telegram-api-token')
        ->getJson('/api/telegram/practices?day=5&locale=ru');

    $response->assertSuccessful()
        ->assertJsonPath('data.0.id', $practice->id)
        ->assertJsonPath('data.0.title', 'Сканирование тела')
        ->assertJsonPath('data.0.description', 'Расслабьте все тело.')
        ->assertJsonPath('data.0.duration_label', '13:00')
        ->assertJsonPath('data.0.focus_problem.title', 'Тревога')
        ->assertJsonPath('data.0.experience_level.title', 'Начальный')
        ->assertJsonPath('data.0.module_choice.title', 'Дыхание')
        ->assertJsonPath('data.0.meditation_type.title', 'С сопровождением');

    expect($response->json('data'))->toHaveCount(1);
});

test('telegram practices api uses simple pagination metadata', function () {
    Practice::factory()->count(2)->create([
        'day' => 5,
        'is_active' => true,
    ]);

    $this->withToken('telegram-api-token')
        ->getJson('/api/telegram/practices?day=5&per_page=1')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonMissingPath('meta.total')
        ->assertJsonMissingPath('meta.last_page');
});

test('telegram practices show endpoint localizes a single practice', function () {
    $practice = Practice::factory()->create([
        'title' => ['en' => 'Evening reset', 'ru' => 'Вечерняя перезагрузка'],
        'description' => ['en' => 'Slow down before sleep.', 'ru' => 'Замедлитесь перед сном.'],
    ]);

    $this->withToken('telegram-api-token')
        ->getJson("/api/telegram/practices/{$practice->id}?locale=ru")
        ->assertSuccessful()
        ->assertJsonPath('data.id', $practice->id)
        ->assertJsonPath('data.title', 'Вечерняя перезагрузка')
        ->assertJsonPath('data.description', 'Замедлитесь перед сном.');
});

test('telegram practices api rejects unsupported locales', function () {
    $this->withToken('telegram-api-token')
        ->getJson('/api/telegram/practices?locale=lv')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['locale']);
});
