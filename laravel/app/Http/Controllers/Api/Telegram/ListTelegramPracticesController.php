<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Telegram\ListTelegramPracticesRequest;
use App\Http\Resources\Api\TelegramPracticeResource;
use App\Models\Practice;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListTelegramPracticesController extends Controller
{
    public function __invoke(ListTelegramPracticesRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();
        $locale = isset($validated['locale']) && is_string($validated['locale'])
            ? $validated['locale']
            : app()->getLocale();
        $request->attributes->set('telegram_locale', $locale);
        $perPage = $this->validatedInt($validated['per_page'] ?? null) ?? 15;

        $practices = Practice::query()
            ->forTelegramDelivery([
                'day' => $this->validatedInt($validated['day'] ?? null),
                'focus_problem_id' => $this->validatedInt($validated['focus_problem_id'] ?? null),
                'experience_level_id' => $this->validatedInt($validated['experience_level_id'] ?? null),
                'module_choice_id' => $this->validatedInt($validated['module_choice_id'] ?? null),
                'meditation_type_id' => $this->validatedInt($validated['meditation_type_id'] ?? null),
            ], $request->boolean('active_only', true))
            ->simplePaginate($perPage)
            ->withQueryString();

        return TelegramPracticeResource::collection($practices);
    }

    private function validatedInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
