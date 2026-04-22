<?php

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use App\Services\Telegram\TelegramBotService;
use Telegram\Bot\Objects\Update as UpdateObject;

beforeEach(function () {
    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);
});

test('telegram webhook rejects requests with an invalid secret token', function () {
    config()->set('services.telegram.webhook_secret', 'expected-secret');

    $this->postJson('/api/telegram/webhook', [])
        ->assertForbidden()
        ->assertJson([
            'message' => __('http-statuses.403'),
        ]);
});

test('telegram webhook answers day commands using practice data', function () {
    config()->set('services.telegram.webhook_secret', 'expected-secret');

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
        'day' => 3,
        'duration' => 600,
        'is_active' => true,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
        'title' => ['en' => 'Breathing reset', 'ru' => 'Перезагрузка дыхания'],
    ]);

    $service = Mockery::mock(TelegramBotService::class);
    $service->shouldReceive('getWebhookUpdate')
        ->once()
        ->andReturn(new UpdateObject([
            'update_id' => 1,
            'message' => [
                'message_id' => 7,
                'date' => now()->timestamp,
                'chat' => ['id' => 123456789, 'type' => 'private'],
                'from' => ['id' => 1, 'is_bot' => false, 'first_name' => 'Ivan', 'language_code' => 'ru'],
                'text' => '/day 3',
            ],
        ]));
    $service->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function (int $chatId, string $text): bool {
            return ($chatId === 123456789)
                && str_contains($text, 'Активные практики для дня 3')
                && str_contains($text, 'Перезагрузка дыхания')
                && str_contains($text, 'Длительность: 10:00');
        });

    $this->app->instance(TelegramBotService::class, $service);

    $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'expected-secret')
        ->postJson('/api/telegram/webhook', [])
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
        ]);
});

test('telegram webhook localizes help messages for supported non-russian locales', function () {
    config()->set('services.telegram.webhook_secret', 'expected-secret');

    $service = Mockery::mock(TelegramBotService::class);
    $service->shouldReceive('getWebhookUpdate')
        ->once()
        ->andReturn(new UpdateObject([
            'update_id' => 2,
            'message' => [
                'message_id' => 8,
                'date' => now()->timestamp,
                'chat' => ['id' => 987654321, 'type' => 'private'],
                'from' => ['id' => 2, 'is_bot' => false, 'first_name' => 'Jonas', 'language_code' => 'lt'],
                'text' => '/help',
            ],
        ]));
    $service->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function (int $chatId, string $text): bool {
            return ($chatId === 987654321)
                && str_contains($text, 'Galimos komandos')
                && str_contains($text, '/day {number}');
        });

    $this->app->instance(TelegramBotService::class, $service);

    $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'expected-secret')
        ->postJson('/api/telegram/webhook', [])
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
        ]);
});
