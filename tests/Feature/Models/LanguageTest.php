<?php

use App\Models\Language;

test('it can create a language', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'is_enabled' => true,
    ]);

    expect($language)
        ->toBeInstanceOf(Language::class)
        ->code->toBe('en')
        ->name->toBe('English')
        ->is_enabled->toBeTrue();
});
