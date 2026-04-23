<?php

use App\Filament\Resources\ExperienceLevelResource\Pages\EditExperienceLevel;
use App\Filament\Resources\ExperienceLevelResource\Pages\ListExperienceLevels;
use App\Filament\Resources\FocusProblemResource\Pages\EditFocusProblem;
use App\Filament\Resources\FocusProblemResource\Pages\ListFocusProblems;
use App\Filament\Resources\MeditationTypeResource\Pages\EditMeditationType;
use App\Filament\Resources\MeditationTypeResource\Pages\ListMeditationTypes;
use App\Filament\Resources\ModuleChoiceResource\Pages\EditModuleChoice;
use App\Filament\Resources\ModuleChoiceResource\Pages\ListModuleChoices;
use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);
});

dataset('taxonomy_resources', [
    'focus problems' => [
        FocusProblem::class,
        ListFocusProblems::class,
        EditFocusProblem::class,
        'focus_problem_id',
    ],
    'experience levels' => [
        ExperienceLevel::class,
        ListExperienceLevels::class,
        EditExperienceLevel::class,
        'experience_level_id',
    ],
    'module choices' => [
        ModuleChoice::class,
        ListModuleChoices::class,
        EditModuleChoice::class,
        'module_choice_id',
    ],
    'meditation types' => [
        MeditationType::class,
        ListMeditationTypes::class,
        EditMeditationType::class,
        'meditation_type_id',
    ],
]);

test('taxonomy resources hide delete actions while practices still reference them', function (
    string $modelClass,
    string $listPageClass,
    string $editPageClass,
    string $ownerForeignKey,
) {
    $record = createTaxonomyOwner($modelClass);
    $unusedRecord = createTaxonomyOwner($modelClass);

    createTaxonomyPractice($ownerForeignKey, taxonomyOwnerKey($record));

    Livewire::test($listPageClass)
        ->assertTableActionHidden('delete', $record)
        ->assertTableActionVisible('delete', $unusedRecord);

    Livewire::test($editPageClass, ['record' => $record->getKey()])
        ->assertActionHidden('delete');

    Livewire::test($editPageClass, ['record' => $unusedRecord->getKey()])
        ->assertActionVisible('delete');
})->with('taxonomy_resources');

test('taxonomy records in use cannot be deleted because practice foreign keys are restrictive', function (
    string $modelClass,
    string $listPageClass,
    string $editPageClass,
    string $ownerForeignKey,
) {
    $record = createTaxonomyOwner($modelClass);
    $practice = createTaxonomyPractice($ownerForeignKey, taxonomyOwnerKey($record));

    expect(fn () => $record->delete())->toThrow(QueryException::class);

    $this->assertModelExists($record);
    $this->assertModelExists($practice);
    expect($practice->fresh()?->getAttribute($ownerForeignKey))->toBe($record->getKey());
})->with('taxonomy_resources');

function createTaxonomyOwner(string $modelClass): ExperienceLevel|FocusProblem|MeditationType|ModuleChoice
{
    return match ($modelClass) {
        FocusProblem::class => FocusProblem::factory()->create(),
        ExperienceLevel::class => ExperienceLevel::factory()->create(),
        ModuleChoice::class => ModuleChoice::factory()->create(),
        MeditationType::class => MeditationType::factory()->create(),
        default => throw new InvalidArgumentException("Unsupported taxonomy model [{$modelClass}]."),
    };
}

function createTaxonomyPractice(string $ownerForeignKey, int $ownerKey): Practice
{
    return Practice::factory()->create([
        'focus_problem_id' => $ownerForeignKey === 'focus_problem_id'
            ? $ownerKey
            : FocusProblem::factory()->create()->getKey(),
        'experience_level_id' => $ownerForeignKey === 'experience_level_id'
            ? $ownerKey
            : ExperienceLevel::factory()->create()->getKey(),
        'module_choice_id' => $ownerForeignKey === 'module_choice_id'
            ? $ownerKey
            : ModuleChoice::factory()->create()->getKey(),
        'meditation_type_id' => $ownerForeignKey === 'meditation_type_id'
            ? $ownerKey
            : MeditationType::factory()->create()->getKey(),
    ]);
}

function taxonomyOwnerKey(Model $model): int
{
    $ownerKey = $model->getKey();

    if (! is_int($ownerKey)) {
        throw new InvalidArgumentException('Expected an integer taxonomy owner key.');
    }

    return $ownerKey;
}
