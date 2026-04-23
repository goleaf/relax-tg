<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TelegramPracticeResource;
use App\Models\Practice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShowTelegramPracticeController extends Controller
{
    public function __invoke(Request $request, Practice $practice): JsonResponse
    {
        $queryLocale = $request->query('locale', app()->getLocale());
        $locale = is_string($queryLocale) ? $queryLocale : app()->getLocale();
        $practiceId = $practice->id;
        $request->attributes->set('telegram_locale', $locale);

        $payload = Cache::remember(
            Practice::telegramShowCacheKey($practiceId, $locale),
            now()->addMinutes(5),
            function () use ($practice, $request): array {
                $practice->loadMissing([
                    'focusProblem:id,title',
                    'experienceLevel:id,title',
                    'moduleChoice:id,title',
                    'meditationType:id,title',
                ]);

                return $this->normalizedPayload(
                    TelegramPracticeResource::make($practice)
                        ->response($request)
                        ->getData(true),
                );
            },
        );

        return response()->json($payload);
    }

    /**
     * @return array<mixed>
     */
    private function normalizedPayload(mixed $payload): array
    {
        return is_array($payload) ? $payload : [];
    }
}
