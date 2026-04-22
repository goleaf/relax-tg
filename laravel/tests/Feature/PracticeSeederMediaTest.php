<?php

use App\Models\Practice;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\PracticeSeeder;
use Illuminate\Support\Facades\Storage;

test('practice seeder generates reusable jpg and mp4 seed media for every practice', function () {
    Storage::fake(Practice::mediaDisk());

    $this->seed([
        LanguageSeeder::class,
        PracticeSeeder::class,
    ]);

    $firstPractice = Practice::query()->orderBy('id')->firstOrFail();

    expect(Practice::query()->whereNotNull('image_path')->count())->toBe(Practice::query()->count())
        ->and(Practice::query()->whereNotNull('video_path')->count())->toBe(Practice::query()->count())
        ->and(Storage::disk(Practice::mediaDisk())->allFiles(Practice::imageDirectory().'/seeded'))->toHaveCount(29)
        ->and(Storage::disk(Practice::mediaDisk())->allFiles(Practice::videoDirectory().'/seeded'))->toHaveCount(29)
        ->and($firstPractice->image_path)->toEndWith('.jpg')
        ->and($firstPractice->video_path)->toEndWith('.mp4')
        ->and(Storage::disk(Practice::mediaDisk())->exists($firstPractice->image_path))->toBeTrue()
        ->and(Storage::disk(Practice::mediaDisk())->exists($firstPractice->video_path))->toBeTrue()
        ->and(Storage::disk(Practice::mediaDisk())->size($firstPractice->image_path))->toBeGreaterThan(0)
        ->and(Storage::disk(Practice::mediaDisk())->size($firstPractice->video_path))->toBeGreaterThan(0);
});
