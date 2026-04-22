<?php

namespace App\Actions\Telegram;

use App\Models\Language;
use App\Models\Practice;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Support\Str;
use Telegram\Bot\Objects\Update as UpdateObject;

class HandleTelegramUpdateAction
{
    public function __construct(
        private readonly TelegramBotService $telegramBotService,
    ) {}

    public function handle(UpdateObject $update): void
    {
        if ($update->objectType() !== 'message') {
            return;
        }

        $message = $update->getMessage();
        $chatId = data_get($update->getChat(), 'id');
        $text = trim((string) $message->get('text', ''));

        if (blank($chatId) || ($text === '')) {
            return;
        }

        $locale = $this->resolveLocale((string) data_get($message->get('from'), 'language_code', ''));
        $command = $this->normalizeCommand($text);

        if ($command === '/start') {
            $this->telegramBotService->sendMessage($chatId, __('telegram.welcome', [], $locale));

            return;
        }

        if ($command === '/help') {
            $this->telegramBotService->sendMessage($chatId, __('telegram.help', [], $locale));

            return;
        }

        if (preg_match('/^\/day\s+(\d{1,2})$/', $command, $matches) === 1) {
            $this->replyWithDayOverview($chatId, (int) $matches[1], $locale);

            return;
        }

        if (preg_match('/^\/practice\s+(\d+)$/', $command, $matches) === 1) {
            $this->replyWithPracticeDetails($chatId, (int) $matches[1], $locale);

            return;
        }

        $this->telegramBotService->sendMessage($chatId, __('telegram.unknown_command', [], $locale));
    }

    private function normalizeCommand(string $text): string
    {
        return (string) preg_replace('/^\/([a-z_]+)@\w+/i', '/$1', Str::lower(trim($text)));
    }

    private function resolveLocale(string $languageCode): string
    {
        $locale = Str::before($languageCode, '-');

        return in_array($locale, Language::supportedInterfaceLocales(), true) ? $locale : 'en';
    }

    private function replyWithDayOverview(int|string $chatId, int $day, string $locale): void
    {
        if (($day < 1) || ($day > 29)) {
            $this->telegramBotService->sendMessage($chatId, __('telegram.invalid_day', [], $locale));

            return;
        }

        $practices = Practice::query()
            ->forTelegramDelivery(['day' => $day])
            ->get();

        if ($practices->isEmpty()) {
            $this->telegramBotService->sendMessage($chatId, __('telegram.day_empty', ['day' => $day], $locale));

            return;
        }

        $message = collect([
            __('telegram.day_intro', ['day' => $day], $locale),
            '',
            $practices
                ->map(fn (Practice $practice): string => $this->formatPracticeSummary($practice, $locale))
                ->implode("\n\n"),
        ])->implode("\n");

        $this->telegramBotService->sendMessage($chatId, trim($message));
    }

    private function replyWithPracticeDetails(int|string $chatId, int $practiceId, string $locale): void
    {
        $practice = Practice::query()
            ->forTelegramDelivery()
            ->find($practiceId);

        if ($practice === null) {
            $this->telegramBotService->sendMessage($chatId, __('telegram.practice_missing', ['id' => $practiceId], $locale));

            return;
        }

        $this->telegramBotService->sendMessage($chatId, $this->formatPracticeDetail($practice, $locale));
    }

    private function formatPracticeSummary(Practice $practice, string $locale): string
    {
        return collect([
            $practice->id.'. '.$practice->getTitle($locale),
            __('telegram.labels.duration', ['value' => Practice::formatDuration($practice->duration)], $locale),
            $this->formatRelationLine('focus_problem', $practice->focusProblem, $locale),
            $this->formatRelationLine('experience_level', $practice->experienceLevel, $locale),
            $this->formatRelationLine('module_choice', $practice->moduleChoice, $locale),
            $this->formatRelationLine('meditation_type', $practice->meditationType, $locale),
        ])->filter()->implode("\n");
    }

    private function formatPracticeDetail(Practice $practice, string $locale): string
    {
        return collect([
            $practice->getTitle($locale),
            __('telegram.labels.day', ['value' => $practice->day], $locale),
            __('telegram.labels.duration', ['value' => Practice::formatDuration($practice->duration)], $locale),
            $this->formatRelationLine('focus_problem', $practice->focusProblem, $locale),
            $this->formatRelationLine('experience_level', $practice->experienceLevel, $locale),
            $this->formatRelationLine('module_choice', $practice->moduleChoice, $locale),
            $this->formatRelationLine('meditation_type', $practice->meditationType, $locale),
            $practice->getDescription($locale),
        ])->filter()->implode("\n");
    }

    private function formatRelationLine(string $key, mixed $relation, string $locale): ?string
    {
        if (($relation === null) || (! method_exists($relation, 'getTitle'))) {
            return null;
        }

        return __("telegram.labels.{$key}", ['value' => $relation->getTitle($locale)], $locale);
    }
}
