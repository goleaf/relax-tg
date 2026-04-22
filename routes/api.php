<?php

use App\Http\Controllers\Api\Telegram\ListTelegramPracticesController;
use App\Http\Controllers\Api\Telegram\ShowTelegramPracticeController;
use App\Http\Controllers\Api\Telegram\TelegramWebhookController;
use App\Http\Middleware\EnsureTelegramApiToken;
use Illuminate\Support\Facades\Route;

Route::prefix('telegram')
    ->name('telegram.')
    ->group(function (): void {
        Route::post('webhook', TelegramWebhookController::class)
            ->name('webhook');

        Route::middleware(EnsureTelegramApiToken::class)
            ->group(function (): void {
                Route::get('practices', ListTelegramPracticesController::class)
                    ->name('practices.index');

                Route::get('practices/{practice}', ShowTelegramPracticeController::class)
                    ->name('practices.show');
            });
    });
