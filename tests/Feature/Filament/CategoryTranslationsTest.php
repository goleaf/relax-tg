<?php

use App\Filament\Resources\ExperienceLevelResource\Pages\CreateExperienceLevel;
use App\Filament\Resources\ExperienceLevelResource\Pages\EditExperienceLevel;
use App\Filament\Resources\ExperienceLevelResource\Pages\ListExperienceLevels;
use App\Filament\Resources\ExperienceLevelResource\RelationManagers\PracticesRelationManager as ExperienceLevelPracticesRelationManager;
use App\Filament\Resources\ExperienceLevelResource;
use App\Filament\Resources\FocusProblemResource\Pages\CreateFocusProblem;
use App\Filament\Resources\FocusProblemResource\Pages\EditFocusProblem;
use App\Filament\Resources\FocusProblemResource\Pages\ListFocusProblems;
use App\Filament\Resources\FocusProblemResource\RelationManagers\PracticesRelationManager as FocusProblemPracticesRelationManager;
use App\Filament\Resources\FocusProblemResource;
use App\Filament\Resources\MeditationTypeResource\Pages\CreateMeditationType;
use App\Filament\Resources\MeditationTypeResource\Pages\EditMeditationType;
use App\Filament\Resources\MeditationTypeResource\Pages\ListMeditationTypes;
use App\Filament\Resources\MeditationTypeResource\RelationManagers\PracticesRelationManager as MeditationTypePracticesRelationManager;
use App\Filament\Resources\MeditationTypeResource;
use App\Filament\Resources\ModuleChoiceResource\Pages\CreateModuleChoice;
use App\Filament\Resources\ModuleChoiceResource\Pages\EditModuleChoice;
use App\Filament\Resources\ModuleChoiceResource\Pages\ListModuleChoices;
use App\Filament\Resources\ModuleChoiceResource\RelationManagers\PracticesRelationManager as ModuleChoicePracticesRelationManager;
use App\Filament\Resources\ModuleChoiceResource;
use App\Models\Practice;
use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);
});

dataset('translated_category_resources', [
    'experience levels' => [
        CreateExperienceLevel::class,
        EditExperienceLevel::class,
        ListExperienceLevels::class,
        ExperienceLevel::class,
        'Beginner',
        'Новичок',
        'Advanced',
        'Продвинутый',
    ],
    'focus problems' => [
        CreateFocusProblem::class,
        EditFocusProblem::class,
        ListFocusProblems::class,
        FocusProblem::class,
        'Stress Relief',
        'Снятие стресса',
        'Focused Calm',
        'Спокойный фокус',
    ],
    'meditation types' => [
        CreateMeditationType::class,
        EditMeditationType::class,
        ListMeditationTypes::class,
        MeditationType::class,
        'Breathwork',
        'Дыхание',
        'Body Scan',
        'Сканирование тела',
    ],
    'module choices' => [
        CreateModuleChoice::class,
        EditModuleChoice::class,
        ListModuleChoices::class,
        ModuleChoice::class,
        'Core Module',
        'Базовый модуль',
        'Nutrition Path',
        'Путь питания',
    ],
]);

test('category resources require titles for every enabled language', function (
    string $createPageClass,
    string $editPageClass,
    string $listPageClass,
    string $modelClass,
) {
    Livewire::test($createPageClass)
        ->assertFormFieldDoesNotExist('slug')
        ->fillForm([
            'title.en' => 'English only',
        ])
        ->call('create')
        ->assertHasFormErrors(['title.ru' => 'required']);

    expect($modelClass::query()->count())->toBe(0);
})->with('translated_category_resources');

test('category resources persist titles for every enabled language', function (
    string $createPageClass,
    string $editPageClass,
    string $listPageClass,
    string $modelClass,
    string $englishTitle,
    string $russianTitle,
    string $updatedEnglishTitle,
    string $updatedRussianTitle,
) {
    Livewire::test($createPageClass)
        ->assertFormFieldDoesNotExist('slug')
        ->fillForm([
            'title.en' => $englishTitle,
            'title.ru' => $russianTitle,
        ])
        ->call('create')
        ->assertHasNoErrors();

    $record = $modelClass::query()->sole();

    expect($record->title['en'])->toBe($englishTitle)
        ->and($record->title['ru'])->toBe($russianTitle)
        ->and($record->slug)->toBe(Str::slug($englishTitle))
        ->and($record->getTitle('fr'))->toBe($englishTitle);

    Livewire::test($editPageClass, ['record' => $record->getKey()])
        ->assertFormFieldDoesNotExist('slug')
        ->fillForm([
            'title.en' => $updatedEnglishTitle,
            'title.ru' => $updatedRussianTitle,
        ])
        ->call('save')
        ->assertHasNoErrors();

    expect($record->fresh()->title['en'])->toBe($updatedEnglishTitle)
        ->and($record->fresh()->title['ru'])->toBe($updatedRussianTitle)
        ->and($record->fresh()->slug)->toBe(Str::slug($updatedEnglishTitle));

    Livewire::test($listPageClass)
        ->assertTableColumnDoesNotExist('slug');
})->with('translated_category_resources');

dataset('category_practice_relation_managers', [
    'experience levels' => [
        ExperienceLevelResource::class,
        EditExperienceLevel::class,
        ExperienceLevelPracticesRelationManager::class,
        ExperienceLevel::class,
        'experience_level_id',
    ],
    'focus problems' => [
        FocusProblemResource::class,
        EditFocusProblem::class,
        FocusProblemPracticesRelationManager::class,
        FocusProblem::class,
        'focus_problem_id',
    ],
    'meditation types' => [
        MeditationTypeResource::class,
        EditMeditationType::class,
        MeditationTypePracticesRelationManager::class,
        MeditationType::class,
        'meditation_type_id',
    ],
    'module choices' => [
        ModuleChoiceResource::class,
        EditModuleChoice::class,
        ModuleChoicePracticesRelationManager::class,
        ModuleChoice::class,
        'module_choice_id',
    ],
]);

test('category resources register practices relation managers', function (
    string $resourceClass,
    string $editPageClass,
    string $relationManagerClass,
    string $modelClass,
    string $ownerForeignKey,
) {
    expect($resourceClass::getRelations())->toContain($relationManagerClass);
})->with('category_practice_relation_managers');

test('category practice relation managers expose practice CRUD tables', function (
    string $resourceClass,
    string $editPageClass,
    string $relationManagerClass,
    string $modelClass,
    string $ownerForeignKey,
) {
    $ownerRecord = $modelClass::factory()->create();

    $practice = Practice::factory()->create([
        'focus_problem_id' => $ownerForeignKey === 'focus_problem_id'
            ? $ownerRecord->getKey()
            : FocusProblem::factory()->create()->getKey(),
        'experience_level_id' => $ownerForeignKey === 'experience_level_id'
            ? $ownerRecord->getKey()
            : ExperienceLevel::factory()->create()->getKey(),
        'module_choice_id' => $ownerForeignKey === 'module_choice_id'
            ? $ownerRecord->getKey()
            : ModuleChoice::factory()->create()->getKey(),
        'meditation_type_id' => $ownerForeignKey === 'meditation_type_id'
            ? $ownerRecord->getKey()
            : MeditationType::factory()->create()->getKey(),
    ]);

    Livewire::test($relationManagerClass, [
        'ownerRecord' => $ownerRecord,
        'pageClass' => $editPageClass,
    ])
        ->assertCanSeeTableRecords([$practice])
        ->assertTableHeaderActionsExistInOrder(['create'])
        ->assertTableActionsExistInOrder(['edit', 'delete']);
})->with('category_practice_relation_managers');
