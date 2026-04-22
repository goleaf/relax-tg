<?php

use App\Models\Language;
use Illuminate\Support\Facades\Lang;

test('framework localization files exist for every supported filament locale', function () {
    foreach (Language::supportedInterfaceLocales() as $locale) {
        expect(Lang::hasForLocale('auth.failed', $locale))->toBeTrue()
            ->and(Lang::hasForLocale('pagination.next', $locale))->toBeTrue()
            ->and(Lang::hasForLocale('passwords.reset', $locale))->toBeTrue()
            ->and(Lang::hasForLocale('validation.accepted', $locale))->toBeTrue()
            ->and(Lang::hasForLocale('http-statuses.401', $locale))->toBeTrue()
            ->and(Lang::hasForLocale('telegram.api.token_not_configured', $locale))->toBeTrue();
    }
});
