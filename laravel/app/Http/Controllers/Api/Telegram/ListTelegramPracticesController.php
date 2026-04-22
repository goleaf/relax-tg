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
        $locale = (string) ($validated['locale'] ?? app()->getLocale());
        $request->attributes->set('telegram_locale', $locale);

        $practices = Practice::query()
            ->forTelegramDelivery($validated, $request->boolean('active_only', true))
            ->simplePaginate(isset($validated['per_page']) ? (int) $validated['per_page'] : 15)
            ->withQueryString();

        return TelegramPracticeResource::collection($practices);
    }
}
