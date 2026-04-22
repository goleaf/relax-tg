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
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
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

test('content translation fields remain driven by enabled languages independently from interface locales', function () {
    Language::query()->create(['code' => 'fr', 'name' => 'French', 'is_enabled' => true]);

    expect(LanguageSwitch::make()->getLocales())->toBe(['en', 'ru']);

    Livewire::test(CreatePractice::class)
        ->assertFormFieldExists('title.en')
        ->assertFormFieldExists('title.ru')
        ->assertFormFieldExists('title.fr')
        ->assertFormFieldExists('description.fr');
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

test('practice list uses inline live filters and hides media and created columns', function () {
    $practice = Practice::factory()->create();

    $component = Livewire::test(ListPractices::class)
        ->assertCanSeeTableRecords([$practice])
        ->assertTableFilterExists('day')
        ->assertTableFilterExists('focus_problem_id')
        ->assertTableFilterExists('experience_level_id')
        ->assertTableFilterExists('module_choice_id')
        ->assertTableFilterExists('meditation_type_id')
        ->assertTableColumnExists('description.en', fn ($column): bool => $column->isHidden())
        ->assertTableColumnDoesNotExist('image_path')
        ->assertTableColumnDoesNotExist('video_path')
        ->assertTableColumnDoesNotExist('created_at');

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

    $table = Livewire::test(ListPractices::class)
        ->instance()
        ->getTable();

    $focusProblemOptions = $table
        ->getFilter('focus_problem_id')
        ->getRelationshipQuery()
        ->get()
        ->mapWithKeys(fn (FocusProblem $record): array => [
            $record->getKey() => Practice::formatCountedLabel(
                $record->getTitle(app()->getLocale()),
                (int) $record->practices_count,
            ),
        ])
        ->all();

    expect($table->getFilter('day')->getOptions())->toMatchArray([
        1 => '1 Day (2)',
        2 => '2 Day (1)',
    ])
        ->and(array_keys($table->getFilters()))->toBe([
            'day',
            'focus_problem_id',
            'experience_level_id',
            'module_choice_id',
            'meditation_type_id',
        ])
        ->and($focusProblemOptions[$focusProblem->id])->toBe('Anxiety (2)')
        ->and($focusProblemOptions[$otherFocusProblem->id])->toBe('Fatigue (1)');
});

test('practice list filter counts reflect active filters while excluding the current dropdown filter', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $otherFocusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Fatigue', 'ru' => 'Усталость'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create([
        'title' => ['en' => 'Beginner', 'ru' => 'Новичок'],
    ]);
    $otherExperienceLevel = ExperienceLevel::factory()->create([
        'title' => ['en' => 'Advanced', 'ru' => 'Продвинутый'],
    ]);
    $moduleChoice = ModuleChoice::factory()->create([
        'title' => ['en' => 'Main', 'ru' => 'Главный'],
    ]);
    $otherModuleChoice = ModuleChoice::factory()->create([
        'title' => ['en' => 'Nutrition', 'ru' => 'Питание'],
    ]);
    $meditationType = MeditationType::factory()->create([
        'title' => ['en' => 'Breath', 'ru' => 'Дыхание'],
    ]);
    $otherMeditationType = MeditationType::factory()->create([
        'title' => ['en' => 'Body', 'ru' => 'Тело'],
    ]);

    Practice::factory()->create([
        'day' => 22,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 23,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 22,
        'focus_problem_id' => $otherFocusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 22,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $otherExperienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 22,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $otherModuleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    Practice::factory()->create([
        'day' => 22,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $otherMeditationType->id,
    ]);

    $component = Livewire::test(ListPractices::class)
        ->filterTable('day', 22)
        ->filterTable('focus_problem_id', $focusProblem->id)
        ->filterTable('experience_level_id', $experienceLevel->id)
        ->filterTable('module_choice_id', $moduleChoice->id)
        ->filterTable('meditation_type_id', $meditationType->id);

    $table = $component->instance()->getTable();

    $focusProblemOptions = $table
        ->getFilter('focus_problem_id')
        ->getRelationshipQuery()
        ->get()
        ->mapWithKeys(fn (FocusProblem $record): array => [
            $record->getKey() => Practice::formatCountedLabel(
                $record->getTitle(app()->getLocale()),
                (int) $record->practices_count,
            ),
        ])
        ->all();

    $experienceLevelOptions = $table
        ->getFilter('experience_level_id')
        ->getRelationshipQuery()
        ->get()
        ->mapWithKeys(fn (ExperienceLevel $record): array => [
            $record->getKey() => Practice::formatCountedLabel(
                $record->getTitle(app()->getLocale()),
                (int) $record->practices_count,
            ),
        ])
        ->all();

    $moduleChoiceOptions = $table
        ->getFilter('module_choice_id')
        ->getRelationshipQuery()
        ->get()
        ->mapWithKeys(fn (ModuleChoice $record): array => [
            $record->getKey() => Practice::formatCountedLabel(
                $record->getTitle(app()->getLocale()),
                (int) $record->practices_count,
            ),
        ])
        ->all();

    $meditationTypeOptions = $table
        ->getFilter('meditation_type_id')
        ->getRelationshipQuery()
        ->get()
        ->mapWithKeys(fn (MeditationType $record): array => [
            $record->getKey() => Practice::formatCountedLabel(
                $record->getTitle(app()->getLocale()),
                (int) $record->practices_count,
            ),
        ])
        ->all();

    expect($table->getFilter('day')->getOptions())->toMatchArray([
        22 => '22 Day (1)',
        23 => '23 Day (1)',
    ])
        ->and($focusProblemOptions[$focusProblem->id])->toBe('Anxiety (1)')
        ->and($focusProblemOptions[$otherFocusProblem->id])->toBe('Fatigue (1)')
        ->and($experienceLevelOptions[$experienceLevel->id])->toBe('Beginner (1)')
        ->and($experienceLevelOptions[$otherExperienceLevel->id])->toBe('Advanced (1)')
        ->and($moduleChoiceOptions[$moduleChoice->id])->toBe('Main (1)')
        ->and($moduleChoiceOptions[$otherModuleChoice->id])->toBe('Nutrition (1)')
        ->and($meditationTypeOptions[$meditationType->id])->toBe('Breath (1)')
        ->and($meditationTypeOptions[$otherMeditationType->id])->toBe('Body (1)');
});

test('practice pages use russian translations when the selected filament locale is russian', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);

    Practice::factory()->create([
        'day' => 22,
        'focus_problem_id' => $focusProblem->id,
    ]);

    $this->withSession(['locale' => 'ru'])
        ->get(PracticeResource::getUrl('create'))
        ->assertSuccessful()
        ->assertSeeText('Создать')
        ->assertSeeText('Общее')
        ->assertSeeText('Категоризация')
        ->assertSeeText('Медиа и длительность')
        ->assertSeeText('Переводы');

    $this->withSession(['locale' => 'ru'])
        ->get(PracticeResource::getUrl('index', [
            'filters' => [
                'day' => ['value' => 22],
                'focus_problem_id' => ['value' => $focusProblem->id],
            ],
        ]))
        ->assertSuccessful()
        ->assertSeeText('Практики')
        ->assertSeeText('Активные фильтры')
        ->assertSeeText('День: 22 день')
        ->assertSeeText('Проблема фокуса: Тревога')
        ->assertSeeText('Удалить фильтр');
});

test('practice list filter indicators show selected values for url filters', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
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
        'day' => 22,
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    $component = Livewire::withQueryParams([
        'filters' => [
            'day' => ['value' => 22],
            'focus_problem_id' => ['value' => $focusProblem->id],
            'experience_level_id' => ['value' => $experienceLevel->id],
            'module_choice_id' => ['value' => $moduleChoice->id],
            'meditation_type_id' => ['value' => $meditationType->id],
        ],
    ])->test(ListPractices::class);

    $indicatorLabels = collect($component->instance()->getTable()->getFilterIndicators())
        ->map(fn ($indicator): string => (string) $indicator->getLabel())
        ->values()
        ->all();

    expect($indicatorLabels)->toContain('Day: 22 Day')
        ->and($indicatorLabels)->toContain('Focus Problem: Anxiety')
        ->and($indicatorLabels)->toContain('Experience Level: Beginner')
        ->and($indicatorLabels)->toContain('Module Choice: Main')
        ->and($indicatorLabels)->toContain('Meditation Type: Breath')
        ->and($indicatorLabels)->not->toContain('Day: 22 Day (1)')
        ->and($indicatorLabels)->not->toContain("Focus Problem: {$focusProblem->id}")
        ->and($indicatorLabels)->not->toContain("Experience Level: {$experienceLevel->id}")
        ->and($indicatorLabels)->not->toContain("Module Choice: {$moduleChoice->id}")
        ->and($indicatorLabels)->not->toContain("Meditation Type: {$meditationType->id}");
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

test('practice form selects show option counts in dropdown labels', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $otherFocusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Fatigue', 'ru' => 'Усталость'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create([
        'title' => ['en' => 'Beginner', 'ru' => 'Новичок'],
    ]);
    $otherExperienceLevel = ExperienceLevel::factory()->create([
        'title' => ['en' => 'Advanced', 'ru' => 'Продвинутый'],
    ]);
    $moduleChoice = ModuleChoice::factory()->create([
        'title' => ['en' => 'Main', 'ru' => 'Главный'],
    ]);
    $otherModuleChoice = ModuleChoice::factory()->create([
        'title' => ['en' => 'Nutrition', 'ru' => 'Питание'],
    ]);
    $meditationType = MeditationType::factory()->create([
        'title' => ['en' => 'Breath', 'ru' => 'Дыхание'],
    ]);
    $otherMeditationType = MeditationType::factory()->create([
        'title' => ['en' => 'Body', 'ru' => 'Тело'],
    ]);

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
        'experience_level_id' => $otherExperienceLevel->id,
        'module_choice_id' => $otherModuleChoice->id,
        'meditation_type_id' => $otherMeditationType->id,
    ]);

    $form = Livewire::test(CreatePractice::class)
        ->instance()
        ->getSchema('form');

    $fields = $form->getFlatFields(withHidden: true);

    expect($fields['day']->getOptions())->toMatchArray([
        1 => '1 Day (2)',
        2 => '2 Day (1)',
    ])
        ->and($fields['focus_problem_id']->getOptions()[$focusProblem->id])->toBe('Anxiety (2)')
        ->and($fields['focus_problem_id']->getOptions()[$otherFocusProblem->id])->toBe('Fatigue (1)')
        ->and($fields['experience_level_id']->getOptions()[$experienceLevel->id])->toBe('Beginner (2)')
        ->and($fields['experience_level_id']->getOptions()[$otherExperienceLevel->id])->toBe('Advanced (1)')
        ->and($fields['module_choice_id']->getOptions()[$moduleChoice->id])->toBe('Main (2)')
        ->and($fields['module_choice_id']->getOptions()[$otherModuleChoice->id])->toBe('Nutrition (1)')
        ->and($fields['meditation_type_id']->getOptions()[$meditationType->id])->toBe('Breath (2)')
        ->and($fields['meditation_type_id']->getOptions()[$otherMeditationType->id])->toBe('Body (1)');
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
