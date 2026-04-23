<?php

use App\Models\Language;

test('it can create a language', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native_name' => 'English',
        'is_enabled' => true,
    ]);

    expect($language)->toBeInstanceOf(Language::class);
    expect($language->code)->toBe('en');
    expect($language->name)->toBe('English');
    expect($language->native_name)->toBe('English');
    expect($language->is_enabled)->toBeTrue();
});

test('it fills the native name when omitted', function () {
    $language = Language::query()->create([
        'code' => 'lt',
        'name' => 'Lithuanian',
        'is_enabled' => true,
    ]);

    expect($language->native_name)->toBe('Lietuvių');
});

test('it refreshes cached enabled language data after updates', function () {
    Language::query()->create([
        'code' => 'en',
        'name' => 'English',
        'native_name' => 'English',
        'is_enabled' => true,
    ]);

    $russian = Language::query()->create([
        'code' => 'ru',
        'name' => 'Russian',
        'native_name' => 'Русский',
        'is_enabled' => false,
    ]);

    expect(Language::enabledCodes())->toBe(['en']);
    expect(Language::enabledCount())->toBe(1);

    $russian->update(['is_enabled' => true]);

    expect(Language::enabledCodes())->toBe(['en', 'ru']);
    expect(Language::enabledCount())->toBe(2);
});
