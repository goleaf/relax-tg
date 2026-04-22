<?php

use App\Enums\ExperienceLevel;
use App\Enums\FocusProblem;
use App\Enums\MeditationType;
use App\Enums\ModuleChoice;
use App\Filament\Resources\Practices\Pages\CreatePractice;
use App\Filament\Resources\Practices\Pages\EditPractice;
use App\Filament\Resources\Practices\Pages\ListPractices;
use App\Filament\Resources\Practices\PracticeResource;
use App\Models\Language;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Ensure at least one enabled language exists for the form tabs.
    Language::firstOrCreate(['code' => 'en'], ['name' => 'English', 'is_enabled' => true]);
});

test('can render practice list page', function () {
    $this->get(PracticeResource::getUrl('index'))
        ->assertSuccessful();
});

test('can list practices', function () {
    $practices = Practice::factory()->count(3)->create();

    Livewire::test(ListPractices::class)
        ->assertCanSeeTableRecords($practices);
});

test('can render practice create page', function () {
    $this->get(PracticeResource::getUrl('create'))
        ->assertSuccessful();
});

test('can create a practice', function () {
    Livewire::test(CreatePractice::class)
        ->fillForm([
            'day' => 1,
            'focus_problem' => FocusProblem::Focus,
            'experience_level' => ExperienceLevel::Beginner,
            'module_choice' => ModuleChoice::Main,
            'meditation_type' => MeditationType::Breath,
            'duration' => 600,
            'title.en' => 'Mindful Breathing',
            'description.en' => 'A simple breathing practice for daily calm.',
        ])
        ->call('create')
        ->assertHasNoErrors();

    expect(Practice::query()
        ->whereJsonContains('title->en', 'Mindful Breathing')
        ->where('focus_problem', FocusProblem::Focus)
        ->exists()
    )->toBeTrue();
});

test('can render practice edit page', function () {
    $practice = Practice::factory()->create();

    $this->get(PracticeResource::getUrl('edit', ['record' => $practice]))
        ->assertSuccessful();
});

test('can edit a practice', function () {
    $practice = Practice::factory()->create();

    Livewire::test(EditPractice::class, ['record' => $practice->getKey()])
        ->fillForm([
            'title.en' => 'Updated Practice Title',
        ])
        ->call('save')
        ->assertHasNoErrors();

    expect($practice->fresh()->title['en'])->toBe('Updated Practice Title');
});

test('can delete a practice', function () {
    $practice = Practice::factory()->create();

    Livewire::test(ListPractices::class)
        ->callTableAction('delete', $practice);

    $this->assertModelMissing($practice);
});
