<?php

use App\Filament\Resources\Practices\Pages\CreatePractice;
use App\Filament\Resources\Practices\Pages\EditPractice;
use App\Filament\Resources\Practices\Pages\ListPractices;
use App\Filament\Resources\Practices\PracticeResource;
use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use App\Models\User;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);
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

test('practice list uses inline live filters with day first and keeps description hidden', function () {
    $practice = Practice::factory()->create();

    $component = Livewire::test(ListPractices::class)
        ->assertCanSeeTableRecords([$practice])
        ->assertTableFilterExists('day')
        ->assertTableFilterExists('focus_problem_id')
        ->assertTableFilterExists('experience_level_id')
        ->assertTableFilterExists('module_choice_id')
        ->assertTableFilterExists('meditation_type_id')
        ->assertTableColumnExists('description.en', fn ($column): bool => $column->isHidden())
        ->assertTableColumnExists('image_path', fn ($column): bool => ! $column->isToggleable())
        ->assertTableColumnExists('video_path', fn ($column): bool => ! $column->isToggleable())
        ->assertTableColumnExists('created_at', fn ($column): bool => ! $column->isToggleable());

    expect($component->instance()->getTable()->getFiltersLayout())->toBe(FiltersLayout::AboveContent)
        ->and($component->instance()->getTable()->hasDeferredFilters())->toBeFalse()
        ->and(array_keys($component->instance()->getTable()->getFilters()))->toBe([
            'day',
            'focus_problem_id',
            'experience_level_id',
            'module_choice_id',
            'meditation_type_id',
        ]);
});

test('practice list can filter by day and related fields and updates the title count', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $otherFocusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Fatigue', 'ru' => 'Усталость'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    $matchingPractice = Practice::factory()->create([
        'day' => 1,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    $filteredOutPractice = Practice::factory()->create([
        'day' => 2,
        'focus_problem_id' => $otherFocusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    $component = Livewire::test(ListPractices::class)
        ->filterTable('day', 1)
        ->filterTable('focus_problem_id', $focusProblem->id)
        ->assertCanSeeTableRecords([$matchingPractice])
        ->assertCanNotSeeTableRecords([$filteredOutPractice]);

    expect($component->instance()->getTitle())->toBe('1 Day / Focus: Anxiety (1)');
});

test('practice list filters show option counts in dropdown labels', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $otherFocusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Fatigue', 'ru' => 'Усталость'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    Practice::factory()->count(2)->create([
        'day' => 1,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 2,
        'focus_problem_id' => $otherFocusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    $component = Livewire::test(ListPractices::class);
    $dayOptions = $component->instance()->getTable()->getFilter('day')->getFormField()->getOptions();
    $focusProblemOptions = $component->instance()->getTable()->getFilter('focus_problem_id')->getFormField()->getOptions();

    expect($dayOptions[1])->toBe('1 Day (2)')
        ->and($dayOptions[2])->toBe('2 Day (1)')
        ->and($focusProblemOptions[$focusProblem->id])->toBe('Anxiety (2)')
        ->and($focusProblemOptions[$otherFocusProblem->id])->toBe('Fatigue (1)');
});

test('can render practice create page', function () {
    $this->get(PracticeResource::getUrl('create'))
        ->assertSuccessful();
});

test('practice create page can prefill values from active filters', function () {
    $focusProblem = FocusProblem::factory()->create();
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    Livewire::withQueryParams([
        'filters' => [
            'day' => ['value' => 7],
            'focus_problem_id' => ['value' => $focusProblem->id],
            'experience_level_id' => ['value' => $experienceLevel->id],
            'module_choice_id' => ['value' => $moduleChoice->id],
            'meditation_type_id' => ['value' => $meditationType->id],
        ],
    ])->test(CreatePractice::class)
        ->assertFormSet([
            'day' => 7,
            'focus_problem_id' => $focusProblem->id,
            'experience_level_id' => $experienceLevel->id,
            'module_choice_id' => $moduleChoice->id,
            'meditation_type_id' => $meditationType->id,
        ]);
});

test('can create a practice with uploaded media', function () {
    Storage::fake(Practice::mediaDisk());

    $focusProblem = FocusProblem::factory()->create();
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();
    $image = UploadedFile::fake()->image('practice-cover.png');
    $video = UploadedFile::fake()->create('practice-intro.mp4', 1024, 'video/mp4');

    Livewire::test(CreatePractice::class)
        ->fillForm([
            'day' => 1,
            'focus_problem_id' => $focusProblem->id,
            'experience_level_id' => $experienceLevel->id,
            'module_choice_id' => $moduleChoice->id,
            'meditation_type_id' => $meditationType->id,
            'duration' => 600,
            'image_path' => $image,
            'video_path' => $video,
            'title.en' => 'Mindful Breathing',
            'title.ru' => 'Осознанное дыхание',
            'description.en' => 'A simple breathing practice for daily calm.',
            'description.ru' => 'Простая дыхательная практика для ежедневного спокойствия.',
        ])
        ->call('create')
        ->assertHasNoErrors();

    $practice = Practice::query()
        ->whereJsonContains('title->en', 'Mindful Breathing')
        ->where('focus_problem_id', $focusProblem->id)
        ->where('experience_level_id', $experienceLevel->id)
        ->where('module_choice_id', $moduleChoice->id)
        ->where('meditation_type_id', $meditationType->id)
        ->where('duration', 600)
        ->firstOrFail();

    expect($practice->image_path)->toStartWith(Practice::imageDirectory().'/')
        ->and($practice->video_path)->toStartWith(Practice::videoDirectory().'/');

    Storage::disk(Practice::mediaDisk())->assertExists($practice->image_path);
    Storage::disk(Practice::mediaDisk())->assertExists($practice->video_path);
});

test('can render practice edit page', function () {
    $practice = Practice::factory()->create();

    $this->get(PracticeResource::getUrl('edit', ['record' => $practice]))
        ->assertSuccessful();
});

test('can edit a practice', function () {
    Storage::fake(Practice::mediaDisk());
    $practice = Practice::factory()->create();

    $newImage = UploadedFile::fake()->image('updated-cover.png');
    $newVideo = UploadedFile::fake()->create('updated-intro.mp4', 1024, 'video/mp4');

    Livewire::test(EditPractice::class, ['record' => $practice->getKey()])
        ->fillForm([
            'title.en' => 'Updated Practice Title',
            'title.ru' => 'Обновленное название практики',
            'image_path' => $newImage,
            'video_path' => $newVideo,
        ])
        ->call('save')
        ->assertHasNoErrors();

    $practice = $practice->fresh();

    expect($practice->title['en'])->toBe('Updated Practice Title')
        ->and($practice->title['ru'])->toBe('Обновленное название практики')
        ->and($practice->image_path)->toStartWith(Practice::imageDirectory().'/')
        ->and($practice->video_path)->toStartWith(Practice::videoDirectory().'/');
    Storage::disk(Practice::mediaDisk())->assertExists($practice->image_path);
    Storage::disk(Practice::mediaDisk())->assertExists($practice->video_path);
});

test('can delete a practice', function () {
    Storage::fake(Practice::mediaDisk());

    $imagePath = Practice::imageDirectory().'/delete-cover.png';
    $videoPath = Practice::videoDirectory().'/delete-intro.mp4';

    Storage::disk(Practice::mediaDisk())->put($imagePath, 'delete-image');
    Storage::disk(Practice::mediaDisk())->put($videoPath, 'delete-video');

    $practice = Practice::factory()->create([
        'image_path' => $imagePath,
        'video_path' => $videoPath,
    ]);

    Livewire::test(ListPractices::class)
        ->callTableAction('delete', $practice);

    $this->assertModelMissing($practice);
    Storage::disk(Practice::mediaDisk())->assertMissing($imagePath);
    Storage::disk(Practice::mediaDisk())->assertMissing($videoPath);
});

test('practice navigation shows flat day items with counts', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $otherFocusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Fatigue', 'ru' => 'Усталость'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create([
        'title' => ['en' => 'Beginner', 'ru' => 'Новичок'],
    ]);
    $moduleChoice = ModuleChoice::factory()->create([
        'title' => ['en' => 'Main', 'ru' => 'Главный'],
    ]);
    $meditationType = MeditationType::factory()->create([
        'title' => ['en' => 'Breath', 'ru' => 'Дыхание'],
    ]);

    Practice::factory()->create([
        'day' => 1,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 1,
        'focus_problem_id' => $otherFocusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    $dayOneNavigationItem = collect(PracticeResource::getNavigationItems())
        ->first(fn ($item) => $item->getLabel() === '1 Day (2)');

    expect($dayOneNavigationItem)->not->toBeNull();
    expect($dayOneNavigationItem->getChildItems())->toBe([]);
});
