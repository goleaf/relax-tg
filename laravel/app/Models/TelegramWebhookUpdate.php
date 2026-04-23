<?php

namespace App\Models;

use Database\Factories\TelegramWebhookUpdateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property int $id
 * @property string $update_key
 * @property int|null $update_id
 * @property string $payload_hash
 * @property int $attempts
 * @property Carbon|null $processing_started_at
 * @property Carbon|null $processed_at
 * @property string|null $last_error
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TelegramWebhookUpdate extends Model
{
    /** @use HasFactory<TelegramWebhookUpdateFactory> */
    use HasFactory;

    private const STALE_PROCESSING_WINDOW_SECONDS = 120;

    protected $fillable = [
        'update_key',
        'update_id',
        'payload_hash',
        'attempts',
        'processing_started_at',
        'processed_at',
        'last_error',
    ];

    protected $casts = [
        'update_id' => 'integer',
        'attempts' => 'integer',
        'processing_started_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function claim(array $payload): ?self
    {
        $updateKey = self::updateKeyFor($payload);
        $updateId = self::normalizeUpdateId(data_get($payload, 'update_id'));
        $payloadHash = self::payloadHash($payload);

        return DB::transaction(function () use ($updateKey, $updateId, $payloadHash): ?self {
            $record = static::query()->createOrFirst(
                ['update_key' => $updateKey],
                [
                    'update_id' => $updateId,
                    'payload_hash' => $payloadHash,
                ],
            );

            /** @var static $record */
            $record = static::query()
                ->whereKey($record->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($record->processed_at !== null) {
                return null;
            }

            if (($record->processing_started_at !== null)
                && $record->processing_started_at->gt(now()->subSeconds(self::STALE_PROCESSING_WINDOW_SECONDS))) {
                return null;
            }

            $record->forceFill([
                'update_id' => $record->update_id ?? $updateId,
                'payload_hash' => $record->payload_hash,
                'attempts' => $record->attempts + 1,
                'processing_started_at' => now(),
                'last_error' => null,
            ])->saveOrFail();

            return $record->refresh();
        }, 5);
    }

    public function markProcessed(): void
    {
        $this->forceFill([
            'processing_started_at' => null,
            'processed_at' => now(),
            'last_error' => null,
        ])->saveOrFail();
    }

    public function markFailed(Throwable $exception): void
    {
        $this->forceFill([
            'processing_started_at' => null,
            'last_error' => $exception->getMessage(),
        ])->saveOrFail();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function updateKeyFor(array $payload): string
    {
        $updateId = self::normalizeUpdateId(data_get($payload, 'update_id'));

        if ($updateId !== null) {
            return 'telegram-update:'.$updateId;
        }

        return 'telegram-update-hash:'.self::payloadHash($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function payloadHash(array $payload): string
    {
        $encodedPayload = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);

        if ($encodedPayload === false) {
            $encodedPayload = '[]';
        }

        return hash('sha256', $encodedPayload);
    }

    private static function normalizeUpdateId(mixed $updateId): ?int
    {
        if (is_int($updateId)) {
            return $updateId;
        }

        if (is_string($updateId) && ctype_digit($updateId)) {
            return (int) $updateId;
        }

        return null;
    }
}
