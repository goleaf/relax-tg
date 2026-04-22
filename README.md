# Relax TG

Relax TG is a Laravel + Filament admin used to manage a multilingual meditation practice program and expose the same content to Telegram clients through a secured internal API and webhook-driven bot flow.

## Project Layout

- `laravel/` — Laravel application
- `laravel/app/Filament` — Filament admin resources, pages, widgets, and table/form schemas
- `laravel/app/Http/Controllers/Api/Telegram` — Telegram webhook and internal API endpoints
- `laravel/app/Actions/Telegram` — Telegram command handling
- `laravel/app/Services/Telegram` — Telegram SDK integration
- `laravel/lang` — application translations
- `.mcp.json` — MCP servers used in this repo

## Stack

- Laravel 13
- Filament 5
- Livewire 4
- Pest 4
- Tailwind CSS 4
- Telegram Bot SDK
- Laravel Boost MCP

## Admin Surface

The top navigation is ordered as:

1. Dashboard
2. Practices
3. Categories
4. Languages

The admin currently manages:

- practices with day, duration, status, image/video media, and multilingual title/description fields
- category taxonomies for focus problems, experience levels, module choices, and meditation types
- enabled content languages
- dashboard widgets for practice coverage, daily volume, average session length, and focus-problem distribution

## Localization

Filament interface locales:

- `de`
- `en`
- `es`
- `fr`
- `it`
- `lt`
- `pl`
- `ru`
- `uk`

Application translation files exist for:

- `admin.php`
- `enums.php`
- `telegram.php`

Filament core package translations come from the installed vendor packages, while project-specific labels and Telegram copy live in `laravel/lang/*`.

## Telegram Integration

Telegram support is already wired into the project:

- MCP server wrapper: `laravel/bin/mcp-telegram`
- MCP registration: `.mcp.json`
- webhook endpoint: `POST /api/telegram/webhook`
- protected internal API:
  - `GET /api/telegram/practices`
  - `GET /api/telegram/practices/{practice}`
- webhook sync command:

```bash
cd laravel
php artisan telegram:webhook:sync --no-interaction
```

Required environment values:

- `TELEGRAM_BOT_TOKEN`
- `TELEGRAM_WEBHOOK_URL`
- `TELEGRAM_WEBHOOK_SECRET`
- `TELEGRAM_INTERNAL_API_TOKEN`

## Query And Data Notes

- practice tables and filters are backed by Eloquent scopes
- practice list filtering is eager-loaded and count-aware in Filament
- Telegram practice delivery uses simple pagination to avoid unnecessary total-count queries
- Telegram list/detail queries share a model scope that selects the API payload columns and eager-loads taxonomy titles
- dashboard aggregates were moved from collection-side grouping into database queries
- migrations add composite indexes for high-traffic practice filters and enabled language tabs
- legacy media URL columns are removed in favor of stored media paths

## Development Commands

```bash
cd laravel
composer install
npm install
php artisan migrate
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run build
```

## Repo Skills

The repo includes local agent skills under `laravel/.agents/skills`, including:

- `laravel-best-practices`
- `pest-testing`
- `tailwindcss-development`
- `telegram-api-development`

## Current Focus Areas

- Filament-first content management
- multilingual admin and Telegram delivery
- predictable Eloquent query patterns with eager loading
- test-backed Telegram webhook and internal API behavior
