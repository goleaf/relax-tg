<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use RuntimeException;

class SyncTelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook:sync
        {--remove : Remove the configured Telegram webhook instead of syncing it.}
        {--drop-pending-updates : Drop pending updates when syncing the webhook.}';

    protected $description = 'Sync or remove the Telegram webhook configured for the default bot.';

    public function handle(TelegramBotService $telegramBotService): int
    {
        try {
            if ($this->option('remove')) {
                if (! $telegramBotService->removeWebhook()) {
                    $this->error('Telegram webhook removal failed.');

                    return self::FAILURE;
                }

                $this->info('Telegram webhook removed.');

                return self::SUCCESS;
            }

            if (! $telegramBotService->syncWebhook((bool) $this->option('drop-pending-updates'))) {
                $this->error('Telegram webhook sync failed.');

                return self::FAILURE;
            }
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Telegram webhook synced.');

        return self::SUCCESS;
    }
}
