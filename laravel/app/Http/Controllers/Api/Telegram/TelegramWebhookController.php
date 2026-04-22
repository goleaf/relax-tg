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
        $configuredSecret = config('services.telegram.webhook_secret');
        $configuredSecret = is_string($configuredSecret) ? $configuredSecret : '';
        $providedSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
        $providedSecret = is_string($providedSecret) ? $providedSecret : '';

        if (($configuredSecret !== '')
            && (! hash_equals($configuredSecret, $providedSecret))) {
            return response()->json([
                'message' => __('http-statuses.403'),
            ], Response::HTTP_FORBIDDEN);
        }

        $handleTelegramUpdateAction->handle($telegramBotService->getWebhookUpdate());

        return response()->json([
            'ok' => true,
        ]);
    }
}
