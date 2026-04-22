<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Actions\Telegram\HandleTelegramUpdateAction;
use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelegramWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        TelegramBotService $telegramBotService,
        HandleTelegramUpdateAction $handleTelegramUpdateAction,
    ): JsonResponse {
        $configuredSecret = (string) config('services.telegram.webhook_secret');

        if (($configuredSecret !== '')
            && (! hash_equals($configuredSecret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token')))) {
            return response()->json([
                'message' => 'Forbidden.',
            ], Response::HTTP_FORBIDDEN);
        }

        $handleTelegramUpdateAction->handle($telegramBotService->getWebhookUpdate());

        return response()->json([
            'ok' => true,
        ]);
    }
}
