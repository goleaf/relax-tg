<?php

namespace App\Http\Resources\Api;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Practice $resource
 */
class TelegramPracticeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributeLocale = $request->attributes->get('telegram_locale');
        $queryLocale = $request->query('locale', app()->getLocale());
        $locale = is_string($attributeLocale) && ($attributeLocale !== '')
            ? $attributeLocale
            : (is_string($queryLocale) ? $queryLocale : app()->getLocale());
        $practice = $this->resource;

        return [
            'id' => $practice->id,
            'day' => $practice->day,
            'duration_seconds' => $practice->duration,
            'duration_label' => Practice::formatDuration($practice->duration),
            'is_active' => $practice->is_active,
            'title' => $practice->getTitle($locale),
            'description' => $practice->getDescription($locale),
            'image_url' => $practice->getImageUrl(),
            'video_url' => $practice->getVideoUrl(),
            'focus_problem' => $this->relationPayload($practice->focusProblem, $locale),
            'experience_level' => $this->relationPayload($practice->experienceLevel, $locale),
            'module_choice' => $this->relationPayload($practice->moduleChoice, $locale),
            'meditation_type' => $this->relationPayload($practice->meditationType, $locale),
        ];
    }

    /**
     * @return array{id: int, title: string}|null
     */
    private function relationPayload(
        FocusProblem|ExperienceLevel|ModuleChoice|MeditationType|null $relation,
        string $locale,
    ): ?array {
        if ($relation === null) {
            return null;
        }

        return [
            'id' => $relation->id,
            'title' => $relation->getTitle($locale),
        ];
    }
}
