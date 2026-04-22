<?php

namespace App\Http\Resources\Api;

use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelegramPracticeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = (string) ($request->attributes->get('telegram_locale') ?: $request->query('locale', app()->getLocale()));

        return [
            'id' => $this->id,
            'day' => $this->day,
            'duration_seconds' => $this->duration,
            'duration_label' => Practice::formatDuration($this->duration),
            'is_active' => $this->is_active,
            'title' => $this->getTitle($locale),
            'description' => $this->getDescription($locale),
            'image_url' => $this->getImageUrl(),
            'video_url' => $this->getVideoUrl(),
            'focus_problem' => $this->relationPayload($this->focusProblem, $locale),
            'experience_level' => $this->relationPayload($this->experienceLevel, $locale),
            'module_choice' => $this->relationPayload($this->moduleChoice, $locale),
            'meditation_type' => $this->relationPayload($this->meditationType, $locale),
        ];
    }

    /**
     * @return array{id: int, title: string}|null
     */
    private function relationPayload(mixed $relation, string $locale): ?array
    {
        if (($relation === null) || (! method_exists($relation, 'getTitle'))) {
            return null;
        }

        return [
            'id' => $relation->getKey(),
            'title' => $relation->getTitle($locale),
        ];
    }
}
