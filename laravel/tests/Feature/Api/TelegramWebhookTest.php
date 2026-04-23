<?php

use App\Jobs\HandleTelegramWebhookUpdateJob;
use Illuminate\Support\Facades\Queue;

test('telegram webhook rejects requests with an invalid secret token', function () {
    config()->set('services.telegram.webhook_secret', 'expected-secret');

    $this->postJson('/api/telegram/webhook', [])
        ->assertForbidden()
        ->assertJson([
            'message' => __('http-statuses.403'),
        ]);
});

test('telegram webhook queues day commands for asynchronous processing', function () {
    config()->set('services.telegram.webhook_secret', 'expected-secret');
    Queue::fake();

    $payload = [
        'update_id' => 1,
        'message' => [
            'message_id' => 7,
            'date' => now()->timestamp,
            'chat' => ['id' => 123456789, 'type' => 'private'],
            'from' => ['id' => 1, 'is_bot' => false, 'first_name' => 'Ivan', 'language_code' => 'ru'],
            'text' => '/day 3',
        ],
    ];

    $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'expected-secret')
        ->postJson('/api/telegram/webhook', $payload)
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'queued' => true,
        ]);

    Queue::assertPushed(HandleTelegramWebhookUpdateJob::class, function (HandleTelegramWebhookUpdateJob $job) use ($payload): bool {
        return $job->payload === $payload;
    });
});

test('telegram webhook queues supported non-russian locale updates without inline processing', function () {
    config()->set('services.telegram.webhook_secret', 'expected-secret');
    Queue::fake();

    $payload = [
        'update_id' => 2,
        'message' => [
            'message_id' => 8,
            'date' => now()->timestamp,
            'chat' => ['id' => 987654321, 'type' => 'private'],
            'from' => ['id' => 2, 'is_bot' => false, 'first_name' => 'Jonas', 'language_code' => 'lt'],
            'text' => '/help',
        ],
    ];

    $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'expected-secret')
        ->postJson('/api/telegram/webhook', $payload)
        ->assertSuccessful()
        ->assertJson([
            'ok' => true,
            'queued' => true,
        ]);

    Queue::assertPushed(HandleTelegramWebhookUpdateJob::class, function (HandleTelegramWebhookUpdateJob $job) use ($payload): bool {
        return $job->payload === $payload;
    });
});
