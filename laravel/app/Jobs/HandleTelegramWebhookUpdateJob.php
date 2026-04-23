<?php

namespace App\Jobs;

use App\Actions\Telegram\HandleTelegramUpdateAction;
use App\Models\TelegramWebhookUpdate;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update as UpdateObject;
use Throwable;

class HandleTelegramWebhookUpdateJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 30;

    public int $uniqueFor = 3600;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly array $payload,
    ) {
        $this->onQueue('telegram');
    }

    /**
     * Execute the job.
     */
    public function handle(HandleTelegramUpdateAction $handleTelegramUpdateAction): void
    {
        $telegramWebhookUpdate = TelegramWebhookUpdate::claim($this->payload);

        if ($telegramWebhookUpdate === null) {
            return;
        }

        try {
            $handleTelegramUpdateAction->handle(new UpdateObject($this->payload));

            $telegramWebhookUpdate->markProcessed();
        } catch (Throwable $exception) {
            $telegramWebhookUpdate->markFailed($exception);

            throw $exception;
        }
    }

    /**
     * @return array<int, RateLimited>
     */
    public function middleware(): array
    {
        return [
            new RateLimited('telegram-bot-updates'),
        ];
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [1, 5, 15, 60];
    }

    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(10);
    }

    public function uniqueId(): string
    {
        $updateId = data_get($this->payload, 'update_id');

        if (is_int($updateId) || (is_string($updateId) && ($updateId !== ''))) {
            return (string) $updateId;
        }

        $encodedPayload = json_encode($this->payload, JSON_INVALID_UTF8_SUBSTITUTE);

        if ($encodedPayload === false) {
            $encodedPayload = '[]';
        }

        return sha1($encodedPayload);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Telegram webhook update processing failed.', [
            'update_id' => data_get($this->payload, 'update_id'),
            'error' => $exception?->getMessage(),
        ]);
    }
}
