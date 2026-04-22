<?php

use App\Services\Telegram\TelegramBotService;

test('telegram webhook sync command syncs the configured webhook', function () {
    $service = Mockery::mock(TelegramBotService::class);
    $service->shouldReceive('syncWebhook')
        ->once()
        ->with(false)
        ->andReturn(true);

    $this->app->instance(TelegramBotService::class, $service);

    $this->artisan('telegram:webhook:sync')
        ->expectsOutput('Telegram webhook synced.')
        ->assertSuccessful();
});
