<?php

use App\Services\Telegram\TelegramBotService;
use Illuminate\Testing\PendingCommand;
use Telegram\Bot\BotsManager;

test('telegram webhook sync command syncs the configured webhook', function () {
    $syncCalls = 0;
    $service = fakeTelegramCommandService(
        syncWebhookHandler: function (bool $dropPendingUpdates) use (&$syncCalls): bool {
            $syncCalls++;

            expect($dropPendingUpdates)->toBeFalse();

            return true;
        },
    );

    $this->instance(TelegramBotService::class, $service);

    $command = $this->artisan('telegram:webhook:sync');

    if (! $command instanceof PendingCommand) {
        throw new RuntimeException('Expected a pending command instance.');
    }

    $command->expectsOutput('Telegram webhook synced.')
        ->run();

    $command->assertSuccessful();

    expect($syncCalls)->toBe(1);
});

/**
 * @param  (Closure(bool): bool)|null  $syncWebhookHandler
 */
function fakeTelegramCommandService(?Closure $syncWebhookHandler = null): TelegramBotService
{
    return new class($syncWebhookHandler) extends TelegramBotService
    {
        /**
         * @param  (Closure(bool): bool)|null  $syncWebhookHandler
         */
        public function __construct(
            private readonly ?Closure $syncWebhookHandler = null,
        ) {
            parent::__construct(new BotsManager([]));
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
