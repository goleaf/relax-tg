<?php

use App\Actions\Telegram\HandleTelegramUpdateAction;
use App\Jobs\HandleTelegramWebhookUpdateJob;
use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use App\Models\TelegramWebhookUpdate;
use App\Services\Telegram\TelegramBotService;
use Telegram\Bot\BotsManager;

test('telegram webhook update job answers day commands from queued payloads', function () {
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

    $sendMessageCalls = 0;
    $service = fakeTelegramJobService(
        sendMessageHandler: function (int|string $chatId, string $text) use (&$sendMessageCalls): void {
            $sendMessageCalls++;

            expect((int) $chatId)->toBe(123456789);
            expect(str_contains($text, 'Активные практики для дня 3'))->toBeTrue();
            expect(str_contains($text, 'Перезагрузка дыхания'))->toBeTrue();
            expect(str_contains($text, 'Длительность: 10:00'))->toBeTrue();
        },
    );

    app()->instance(TelegramBotService::class, $service);

    $job = new HandleTelegramWebhookUpdateJob([
        'update_id' => 1,
        'message' => [
            'message_id' => 7,
            'date' => now()->timestamp,
            'chat' => ['id' => 123456789, 'type' => 'private'],
            'from' => ['id' => 1, 'is_bot' => false, 'first_name' => 'Ivan', 'language_code' => 'ru'],
            'text' => '/day 3',
        ],
    ]);

    $job->handle(app(HandleTelegramUpdateAction::class));

    expect($sendMessageCalls)->toBe(1);
});

test('telegram webhook update job localizes help messages for supported locales', function () {
    $sendMessageCalls = 0;
    $service = fakeTelegramJobService(
        sendMessageHandler: function (int|string $chatId, string $text) use (&$sendMessageCalls): void {
            $sendMessageCalls++;

            expect((int) $chatId)->toBe(987654321);
            expect(str_contains($text, 'Galimos komandos'))->toBeTrue();
            expect(str_contains($text, '/day {number}'))->toBeTrue();
        },
    );

    app()->instance(TelegramBotService::class, $service);

    $job = new HandleTelegramWebhookUpdateJob([
        'update_id' => 2,
        'message' => [
            'message_id' => 8,
            'date' => now()->timestamp,
            'chat' => ['id' => 987654321, 'type' => 'private'],
            'from' => ['id' => 2, 'is_bot' => false, 'first_name' => 'Jonas', 'language_code' => 'lt'],
            'text' => '/help',
        ],
    ]);

    $job->handle(app(HandleTelegramUpdateAction::class));

    expect($sendMessageCalls)->toBe(1);
});

test('telegram webhook update job processes a duplicate update only once', function () {
    $sendMessageCalls = 0;
    $service = fakeTelegramJobService(
        sendMessageHandler: function (int|string $chatId, string $text) use (&$sendMessageCalls): void {
            $sendMessageCalls++;

            expect((int) $chatId)->toBe(111222333);
            expect(str_contains($text, 'Available commands'))->toBeTrue();
            expect(str_contains($text, '/practice {id}'))->toBeTrue();
        },
    );

    app()->instance(TelegramBotService::class, $service);

    $payload = [
        'update_id' => 55,
        'message' => [
            'message_id' => 9,
            'date' => now()->timestamp,
            'chat' => ['id' => 111222333, 'type' => 'private'],
            'from' => ['id' => 3, 'is_bot' => false, 'first_name' => 'Anna', 'language_code' => 'en'],
            'text' => '/help',
        ],
    ];

    (new HandleTelegramWebhookUpdateJob($payload))->handle(app(HandleTelegramUpdateAction::class));
    (new HandleTelegramWebhookUpdateJob($payload))->handle(app(HandleTelegramUpdateAction::class));

    $record = TelegramWebhookUpdate::query()->sole();

    expect($record->update_key)->toBe('telegram-update:55');
    expect($record->attempts)->toBe(1);
    expect($record->processed_at)->not->toBeNull();
    expect($record->last_error)->toBeNull();
    expect($sendMessageCalls)->toBe(1);
});

test('telegram webhook update job releases failed idempotency claims for retries', function () {
    $payload = [
        'update_id' => 77,
        'message' => [
            'message_id' => 10,
            'date' => now()->timestamp,
            'chat' => ['id' => 444555666, 'type' => 'private'],
            'from' => ['id' => 4, 'is_bot' => false, 'first_name' => 'Mila', 'language_code' => 'en'],
            'text' => '/help',
        ],
    ];

    $failingService = fakeTelegramJobService(
        sendMessageHandler: telegramFailureHandler('Temporary Telegram outage.'),
    );

    app()->instance(TelegramBotService::class, $failingService);

    expect(fn () => (new HandleTelegramWebhookUpdateJob($payload))->handle(app(HandleTelegramUpdateAction::class)))
        ->toThrow(RuntimeException::class, 'Temporary Telegram outage.');

    $failedRecord = TelegramWebhookUpdate::query()->sole();

    expect($failedRecord->processed_at)->toBeNull();
    expect($failedRecord->processing_started_at)->toBeNull();
    expect($failedRecord->attempts)->toBe(1);
    expect($failedRecord->last_error)->toBe('Temporary Telegram outage.');

    $sendMessageCalls = 0;
    $successfulService = fakeTelegramJobService(
        sendMessageHandler: function (int|string $chatId, string $text) use (&$sendMessageCalls): void {
            $sendMessageCalls++;

            expect((int) $chatId)->toBe(444555666);
            expect(str_contains($text, 'Available commands'))->toBeTrue();
        },
    );

    app()->instance(TelegramBotService::class, $successfulService);

    (new HandleTelegramWebhookUpdateJob($payload))->handle(app(HandleTelegramUpdateAction::class));

    $retriedRecord = TelegramWebhookUpdate::query()->sole();

    expect($retriedRecord->processed_at)->not->toBeNull();
    expect($retriedRecord->processing_started_at)->toBeNull();
    expect($retriedRecord->attempts)->toBe(2);
    expect($retriedRecord->last_error)->toBeNull();
    expect($sendMessageCalls)->toBe(1);
});

/**
 * @param  (Closure(int|string, string, array<string, mixed>): void)|null  $sendMessageHandler
 * @param  (Closure(bool): bool)|null  $syncWebhookHandler
 */
function fakeTelegramJobService(?Closure $sendMessageHandler = null, ?Closure $syncWebhookHandler = null): TelegramBotService
{
    return new class($sendMessageHandler, $syncWebhookHandler) extends TelegramBotService
    {
        /**
         * @param  (Closure(int|string, string, array<string, mixed>): void)|null  $sendMessageHandler
         * @param  (Closure(bool): bool)|null  $syncWebhookHandler
         */
        public function __construct(
            private readonly ?Closure $sendMessageHandler = null,
            private readonly ?Closure $syncWebhookHandler = null,
        ) {
            parent::__construct(new BotsManager([]));
        }

        /**
         * @param  array<string, mixed>  $extra
         */
        public function sendMessage(int|string $chatId, string $text, array $extra = []): void
        {
            if ($this->sendMessageHandler === null) {
                throw new RuntimeException('Unexpected Telegram message dispatch.');
            }

            ($this->sendMessageHandler)($chatId, $text, $extra);
        }

        public function syncWebhook(bool $dropPendingUpdates = false): bool
        {
            if ($this->syncWebhookHandler === null) {
                return true;
            }

            return ($this->syncWebhookHandler)($dropPendingUpdates);
        }
    };
}

/**
 * @return Closure(int|string, string, array<string, mixed>): void
 */
function telegramFailureHandler(string $message): Closure
{
    return function () use ($message): void {
        throw new RuntimeException($message);
    };
}
