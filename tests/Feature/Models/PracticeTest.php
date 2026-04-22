<?php

use App\Models\Practice;

test('it can create a practice with translated fields', function () {
    $practice = Practice::create([
        'title' => ['en' => 'Morning Meditation', 'sk' => 'Ranná meditácia'],
        'description' => ['en' => 'A calming morning practice.', 'sk' => 'Upokojujúca ranná prax.'],
    ]);

    expect($practice)
        ->toBeInstanceOf(Practice::class)
        ->and($practice->title['en'])->toBe('Morning Meditation')
        ->and($practice->title['sk'])->toBe('Ranná meditácia');
});

test('it returns the correct title for the requested locale', function () {
    $practice = Practice::factory()->create([
        'title' => ['en' => 'Breathing Exercise', 'sk' => 'Dychové cvičenie'],
    ]);

    expect($practice->getTitle('en'))->toBe('Breathing Exercise')
        ->and($practice->getTitle('sk'))->toBe('Dychové cvičenie');
});

test('it falls back to english when locale is missing', function () {
    $practice = Practice::factory()->create([
        'title' => ['en' => 'Body Scan'],
        'description' => ['en' => 'A mindfulness body scan practice.'],
    ]);

    expect($practice->getTitle('fr'))->toBe('Body Scan')
        ->and($practice->getDescription('fr'))->toBe('A mindfulness body scan practice.');
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
