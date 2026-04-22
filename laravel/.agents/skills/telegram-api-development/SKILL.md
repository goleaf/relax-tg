---
name: telegram-api-development
description: Use when building or extending this project's Telegram bot, webhook, internal Telegram API, or Telegram MCP workflow. Covers the existing webhook controller, secured `/api/telegram/*` endpoints, `telegram:webhook:sync` artisan command, localized bot messages, and the `./bin/mcp-telegram` MCP wrapper already configured in the repo root `.mcp.json`.
metadata:
  short-description: Work on this project's Telegram bot and API stack
---

# Telegram API Development

Use this skill whenever the task involves Telegram bots, Telegram webhooks, Telegram API payloads, or Telegram MCP access in this repository.

## Existing Project Surface

- MCP server: repo root `.mcp.json` registers `telegram` via `./bin/mcp-telegram`
- MCP wrapper: [bin/mcp-telegram](/Users/andrejprus/Herd/relax-tg/laravel/bin/mcp-telegram)
- Webhook route: [routes/api.php](/Users/andrejprus/Herd/relax-tg/laravel/routes/api.php)
- Webhook controller: [app/Http/Controllers/Api/Telegram/TelegramWebhookController.php](/Users/andrejprus/Herd/relax-tg/laravel/app/Http/Controllers/Api/Telegram/TelegramWebhookController.php)
- Bot action: [app/Actions/Telegram/HandleTelegramUpdateAction.php](/Users/andrejprus/Herd/relax-tg/laravel/app/Actions/Telegram/HandleTelegramUpdateAction.php)
- Bot service: [app/Services/Telegram/TelegramBotService.php](/Users/andrejprus/Herd/relax-tg/laravel/app/Services/Telegram/TelegramBotService.php)
- Internal API resource: [app/Http/Resources/Api/TelegramPracticeResource.php](/Users/andrejprus/Herd/relax-tg/laravel/app/Http/Resources/Api/TelegramPracticeResource.php)
- Webhook sync command: [app/Console/Commands/SyncTelegramWebhookCommand.php](/Users/andrejprus/Herd/relax-tg/laravel/app/Console/Commands/SyncTelegramWebhookCommand.php)

## Current Behavior

- `/api/telegram/webhook` accepts Telegram updates and verifies `X-Telegram-Bot-Api-Secret-Token`
- `/api/telegram/practices` and `/api/telegram/practices/{practice}` expose Filament-managed practice content for Telegram clients
- Telegram locale handling is limited to the supported interface locales in `App\Models\Language::supportedInterfaceLocales()`
- Bot copy lives in `lang/*/telegram.php`
- Practice payload localization comes from model translation accessors and eager-loaded taxonomy relations

## Default Workflow

1. Inspect `routes/api.php`, the Telegram controller, and `HandleTelegramUpdateAction` before changing behavior.
2. Reuse `TelegramBotService` for outbound API calls; do not scatter direct Telegram SDK calls across controllers or actions.
3. Keep locale support aligned with `App\Models\Language::supportedInterfaceLocales()`.
4. For new bot commands, add request handling in `HandleTelegramUpdateAction`, then add or update bot copy in every supported `lang/*/telegram.php` file.
5. For new Telegram-facing payload fields, update `TelegramPracticeResource` and extend the feature tests instead of using ad hoc arrays in controllers.
6. When webhook URLs or secrets change, use `php artisan telegram:webhook:sync --no-interaction` instead of manual API calls.

## Environment And Config

- `TELEGRAM_BOT_TOKEN`
- `TELEGRAM_WEBHOOK_URL`
- `TELEGRAM_WEBHOOK_SECRET`
- `TELEGRAM_INTERNAL_API_TOKEN`

These values are consumed through `config/telegram.php`, `config/services.php`, and the MCP wrapper script. Do not hardcode them.

## MCP Notes

- The MCP server is already registered; do not add a second Telegram MCP server unless the existing wrapper is being replaced intentionally.
- `bin/mcp-telegram` currently launches `@iqai/mcp-telegram` and requires `TELEGRAM_BOT_TOKEN`.
- If MCP behavior changes, update both the wrapper script and the repo root `.mcp.json`.

## Primary References

- Telegram Bot API: https://core.telegram.org/bots/api
- BotFather and bot setup: https://core.telegram.org/bots#botfather
- Current MCP package used by this repo: https://www.npmjs.com/package/@iqai/mcp-telegram

## Testing

- API tests: [tests/Feature/Api/TelegramPracticeApiTest.php](/Users/andrejprus/Herd/relax-tg/laravel/tests/Feature/Api/TelegramPracticeApiTest.php)
- Webhook tests: [tests/Feature/Api/TelegramWebhookTest.php](/Users/andrejprus/Herd/relax-tg/laravel/tests/Feature/Api/TelegramWebhookTest.php)
- Command tests: [tests/Feature/Console/SyncTelegramWebhookCommandTest.php](/Users/andrejprus/Herd/relax-tg/laravel/tests/Feature/Console/SyncTelegramWebhookCommandTest.php)

Always update or add tests when changing Telegram routes, locale handling, commands, or webhook behavior.
