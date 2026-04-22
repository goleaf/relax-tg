<?php

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Languages\LanguageResource;
use App\Filament\Resources\Practices\PracticeResource;
use App\Filament\Widgets\FocusProblemDistributionChart;
use App\Filament\Widgets\PracticeDurationChart;
use App\Filament\Widgets\PracticeOverviewStats;
use App\Filament\Widgets\PracticeVolumeChart;
use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);
});

test('dashboard renders custom widgets and removes the default dashboard cards', function () {
    $focusProblem = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    Practice::factory()->count(2)->create([
        'focus_problem_id' => $focusProblem->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSeeText('Program Snapshot')
        ->assertSeeText('Enabled languages')
        ->assertSeeText('Practice Volume by Day')
        ->assertSeeText('Average Session Length')
        ->assertSeeText('Focus Problem Mix')
        ->assertDontSeeText('Documentation')
        ->assertDontSeeText('Welcome');
});

test('dashboard, practices, categories, and languages are rendered in the plugin top navigation order', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee('data-test-topbar-primary-navigation', false)
        ->assertSee('class="fi-topbar-nav-groups"', false)
        ->assertSee('data-test-topbar-link="dashboard"', false)
        ->assertSee('data-test-topbar-link="practices"', false)
        ->assertSee('data-test-topbar-link="categories"', false)
        ->assertSee('data-test-topbar-link="languages"', false)
        ->assertSeeText('Dashboard')
        ->assertSeeText('Practices')
        ->assertSeeText('Categories')
        ->assertSeeText('Languages');

    $topNavigationLabels = collect(filament()->getCurrentOrDefaultPanel()->buildNavigation())
        ->flatMap(fn ($group): array => filled($group->getLabel())
            ? [$group->getLabel()]
            : collect($group->getItems())
                ->map(fn ($item): string => $item->getLabel())
                ->all())
        ->values()
        ->all();

    expect(Dashboard::shouldRegisterNavigation())->toBeTrue()
        ->and(PracticeResource::shouldRegisterNavigation())->toBeTrue()
        ->and($topNavigationLabels)->toBe([
            'Dashboard',
            'Practices',
            'Categories',
            'Languages',
        ])
        ->and(LanguageResource::shouldRegisterNavigation())->toBeTrue();
});

test('dashboard widgets summarize practice metrics', function () {
    $focusProblemA = FocusProblem::factory()->create([
        'title' => ['en' => 'Anxiety', 'ru' => 'Тревога'],
    ]);
    $focusProblemB = FocusProblem::factory()->create([
        'title' => ['en' => 'Focus', 'ru' => 'Фокус'],
    ]);
    $experienceLevel = ExperienceLevel::factory()->create();
    $moduleChoice = ModuleChoice::factory()->create();
    $meditationType = MeditationType::factory()->create();

    Practice::factory()->create([
        'day' => 1,
        'focus_problem_id' => $focusProblemA->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
        'duration' => 600,
        'image_path' => 'practices/images/example-1.jpg',
        'video_path' => 'practices/videos/example-1.mp4',
    ]);

    Practice::factory()->create([
        'day' => 2,
        'focus_problem_id' => $focusProblemA->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
        'duration' => 900,
        'image_path' => 'practices/images/example-2.jpg',
        'video_path' => 'practices/videos/example-2.mp4',
    ]);

    Practice::factory()->create([
        'day' => 2,
        'focus_problem_id' => $focusProblemB->id,
        'experience_level_id' => $experienceLevel->id,
        'module_choice_id' => $moduleChoice->id,
        'meditation_type_id' => $meditationType->id,
        'duration' => 300,
    ]);

    Livewire::test(PracticeOverviewStats::class)
        ->assertSeeText('Program Snapshot')
        ->assertSeeText('Total practices')
        ->assertSeeText('Enabled languages')
        ->assertSeeText('Days covered')
        ->assertSeeText('Media ready')
        ->assertSeeText('Average session')
        ->assertSeeText('2/29')
        ->assertSeeText('67% include both image and video')
        ->assertSeeText('10:00');

    Livewire::test(PracticeVolumeChart::class)
        ->assertSeeText('Practice Volume by Day');

    Livewire::test(PracticeDurationChart::class)
        ->assertSeeText('Average Session Length');

    Livewire::test(FocusProblemDistributionChart::class)
        ->assertSeeText('Focus Problem Mix');
});

test('dashboard uses russian translations when the selected filament locale is russian', function () {
    $this->withSession(['locale' => 'ru'])
        ->get('/')
        ->assertSuccessful()
        ->assertSeeText('Инфопанель')
        ->assertSeeText('Сводка программы')
        ->assertSeeText('Включенные языки')
        ->assertSeeText('Количество практик по дням')
        ->assertSeeText('Средняя длительность сессии')
        ->assertSeeText('Распределение по фокусу');
});
