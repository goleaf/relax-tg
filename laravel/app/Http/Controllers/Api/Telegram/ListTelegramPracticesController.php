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
            ->selectResourceColumns()
            ->withTaxonomyTitles()
            ->when(
                $request->boolean('active_only', true),
                fn ($query) => $query->active(),
            )
            ->forDay(isset($validated['day']) ? (int) $validated['day'] : null)
            ->forFocusProblem(isset($validated['focus_problem_id']) ? (int) $validated['focus_problem_id'] : null)
            ->forExperienceLevel(isset($validated['experience_level_id']) ? (int) $validated['experience_level_id'] : null)
            ->forModuleChoice(isset($validated['module_choice_id']) ? (int) $validated['module_choice_id'] : null)
            ->forMeditationType(isset($validated['meditation_type_id']) ? (int) $validated['meditation_type_id'] : null)
            ->orderedForProgram()
            ->paginate(isset($validated['per_page']) ? (int) $validated['per_page'] : 15)
            ->withQueryString();

        return TelegramPracticeResource::collection($practices);
    }
}
