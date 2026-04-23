<?php

namespace Database\Factories;

use App\Models\TelegramWebhookUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TelegramWebhookUpdate>
 */
class TelegramWebhookUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'update_key' => 'telegram-update:'.$this->faker->unique()->numberBetween(1, 1_000_000),
            'update_id' => $this->faker->unique()->numberBetween(1, 1_000_000),
            'payload_hash' => hash('sha256', $this->faker->uuid()),
            'attempts' => 0,
            'processing_started_at' => null,
            'processed_at' => null,
            'last_error' => null,
        ];
    }
}
