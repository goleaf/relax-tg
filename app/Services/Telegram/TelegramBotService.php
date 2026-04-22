<?php

namespace App\Services\Telegram;

use Illuminate\Support\Str;
use RuntimeException;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Objects\Update as UpdateObject;

class TelegramBotService
{
    public function __construct(
        private readonly BotsManager $botsManager,
    ) {}

    public function bot(): Api
    {
        return $this->botsManager->bot();
    }

    public function getWebhookUpdate(): UpdateObject
    {
        return $this->bot()->getWebhookUpdate(false);
    }

    public function sendMessage(int|string $chatId, string $text, array $extra = []): void
    {
        $this->bot()->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            ...$extra,
        ]);
    }

    public function syncWebhook(bool $dropPendingUpdates = false): bool
    {
        $botConfig = $this->botsManager->getBotConfig();
        $webhookUrl = (string) data_get($botConfig, 'webhook_url');

        if (! Str::startsWith($webhookUrl, 'https://')) {
            throw new RuntimeException('TELEGRAM_WEBHOOK_URL must be a valid https:// URL.');
        }

        $params = [
            'url' => $webhookUrl,
        ];

        $certificatePath = data_get($botConfig, 'certificate_path');

        if (filled($certificatePath)) {
            $params['certificate'] = $certificatePath;
        }

        $allowedUpdates = data_get($botConfig, 'allowed_updates');

        if (filled($allowedUpdates)) {
            $params['allowed_updates'] = $allowedUpdates;
        }

        $webhookSecret = (string) config('services.telegram.webhook_secret');

        if ($webhookSecret !== '') {
            $params['secret_token'] = $webhookSecret;
        }

        if ($dropPendingUpdates) {
            $params['drop_pending_updates'] = true;
        }

        return $this->bot()->setWebhook($params);
    }

    public function removeWebhook(): bool
    {
        return $this->bot()->removeWebhook();
    }
}
