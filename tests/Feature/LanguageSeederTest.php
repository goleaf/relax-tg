<?php

use App\Models\Language;
use Database\Seeders\LanguageSeeder;

test('language seeder enables only english and russian by default', function () {
    Language::factory()->create([
        'code' => 'es',
        'name' => 'Spanish',
        'is_enabled' => true,
    ]);

    $this->seed(LanguageSeeder::class);

    expect(Language::query()->count())->toBe(count(config('languages')))
        ->and(Language::query()->where('code', 'en')->value('is_enabled'))->toBeTrue()
        ->and(Language::query()->where('code', 'ru')->value('is_enabled'))->toBeTrue()
        ->and(Language::query()->where('code', 'es')->value('is_enabled'))->toBeFalse()
        ->and(Language::enabled()->pluck('code')->sort()->values()->all())->toBe(['en', 'ru']);
});
