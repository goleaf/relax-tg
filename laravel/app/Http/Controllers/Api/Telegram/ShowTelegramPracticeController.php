<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TelegramPracticeResource;
use App\Models\Practice;
use Illuminate\Http\Request;

class ShowTelegramPracticeController extends Controller
{
    public function __invoke(Request $request, Practice $practice): TelegramPracticeResource
    {
        $request->attributes->set('telegram_locale', $request->query('locale', app()->getLocale()));

        $practice->loadMissing([
            'focusProblem:id,title',
            'experienceLevel:id,title',
            'moduleChoice:id,title',
            'meditationType:id,title',
        ]);

        return TelegramPracticeResource::make($practice);
    }
}
