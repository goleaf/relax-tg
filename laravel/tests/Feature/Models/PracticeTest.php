<?php

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use Illuminate\Support\Facades\Storage;

test('it can create a practice with translated fields', function () {
    $focusProblem = FocusProblem::factory()->create();
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    $practice = Practice::create([
        'day' => 1,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
        'duration' => 600,
        'image_path' => 'practices/images/practice.jpg',
        'video_path' => 'practices/videos/practice.mp4',
        'title' => ['en' => 'Morning Meditation', 'ru' => 'Утренняя медитация'],
        'description' => ['en' => 'A calming morning practice.', 'ru' => 'Успокаивающая утренняя практика.'],
    ]);

    expect($practice)
        ->toBeInstanceOf(Practice::class)
        ->and($practice->fresh()->focus_problem_id)->toBe($focusProblem->id)
        ->and($practice->fresh()->experience_level_id)->toBe($experienceLevel->id)
        ->and($practice->fresh()->module_choice_id)->toBe($moduleChoice->id)
        ->and($practice->fresh()->meditation_type_id)->toBe($meditationType->id)
        ->and($practice->fresh()->duration)->toBe(600)
        ->and($practice->fresh()->image_path)->toBe('practices/images/practice.jpg')
        ->and($practice->fresh()->video_path)->toBe('practices/videos/practice.mp4')
        ->and($practice->title['en'])->toBe('Morning Meditation')
        ->and($practice->title['ru'])->toBe('Утренняя медитация');
});

test('it can generate public media urls for uploaded files', function () {
    Storage::fake(Practice::mediaDisk());

    $practice = Practice::factory()->make([
        'image_path' => 'practices/images/example.jpg',
        'video_path' => 'practices/videos/example.mp4',
    ]);

    expect($practice->getImageUrl())->toContain('/storage/practices/images/example.jpg')
        ->and($practice->getVideoUrl())->toContain('/storage/practices/videos/example.mp4');
});

test('it deletes replaced media files when stored paths change', function () {
    Storage::fake(Practice::mediaDisk());

    $oldImagePath = Practice::imageDirectory().'/old-cover.png';
    $oldVideoPath = Practice::videoDirectory().'/old-intro.mp4';
    $newImagePath = Practice::imageDirectory().'/new-cover.png';
    $newVideoPath = Practice::videoDirectory().'/new-intro.mp4';

    Storage::disk(Practice::mediaDisk())->put($oldImagePath, 'old-image');
    Storage::disk(Practice::mediaDisk())->put($oldVideoPath, 'old-video');
    Storage::disk(Practice::mediaDisk())->put($newImagePath, 'new-image');
    Storage::disk(Practice::mediaDisk())->put($newVideoPath, 'new-video');

    $practice = Practice::factory()->create([
        'image_path' => $oldImagePath,
        'video_path' => $oldVideoPath,
    ]);

    $practice->update([
        'image_path' => $newImagePath,
        'video_path' => $newVideoPath,
    ]);

    Storage::disk(Practice::mediaDisk())->assertMissing($oldImagePath);
    Storage::disk(Practice::mediaDisk())->assertMissing($oldVideoPath);
    Storage::disk(Practice::mediaDisk())->assertExists($newImagePath);
    Storage::disk(Practice::mediaDisk())->assertExists($newVideoPath);
});

test('it returns the correct title for the requested locale', function () {
    $practice = Practice::factory()->create([
        'title' => ['en' => 'Breathing Exercise', 'ru' => 'Дыхательное упражнение'],
    ]);

    expect($practice->getTitle('en'))->toBe('Breathing Exercise')
        ->and($practice->getTitle('ru'))->toBe('Дыхательное упражнение');
});

test('it falls back to english title when the requested locale is missing', function () {
    $practice = Practice::factory()->create([
        'title' => ['en' => 'Body Scan'],
        'description' => ['en' => 'A mindfulness body scan practice.'],
    ]);

    expect($practice->getTitle('fr'))->toBe('Body Scan');
});

test('it falls back to english description when locale is missing', function () {
    $practice = Practice::factory()->create([
        'title' => ['en' => 'Body Scan'],
        'description' => ['en' => 'A mindfulness body scan practice.'],
    ]);

    expect($practice->getDescription('fr'))->toBe('A mindfulness body scan practice.');
});

test('it returns null description when not set', function () {
    $practice = Practice::factory()->create([
        'description' => null,
    ]);

    expect($practice->getDescription('en'))->toBeNull();
});

test('it can be queried with latestFirst scope', function () {
    $first = Practice::factory()->create();
    $second = Practice::factory()->create();

    $results = Practice::latestFirst()->get();

    expect($results->first()->id)->toBe($second->id);
});

test('it can filter practices through taxonomy scopes', function () {
    $focusProblem = FocusProblem::factory()->create();
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    $matchingPractice = Practice::factory()->create([
        'day' => 4,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create();

    $results = Practice::query()
        ->forDay(4)
        ->forFocusProblem($focusProblem->id)
        ->forExperienceLevel($experienceLevel->id)
        ->forModuleChoice($moduleChoice->id)
        ->forMeditationType($meditationType->id)
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->is($matchingPractice))->toBeTrue();
});

test('it can aggregate navigation counts by day', function () {
    Practice::factory()->count(2)->create(['day' => 1]);
    Practice::factory()->create(['day' => 3]);

    $counts = Practice::query()
        ->selectDayCounts()
        ->pluck('total', 'day');

    expect((int) $counts[1])->toBe(2)
        ->and((int) $counts[3])->toBe(1)
        ->and(isset($counts[2]))->toBeFalse();
});
