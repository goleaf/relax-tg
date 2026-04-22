<?php

use App\Filament\Resources\Languages\LanguageResource;
use App\Filament\Resources\Languages\Pages\ListLanguages;
use App\Models\Language;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render languages list page', function () {
    $this->get(LanguageResource::getUrl('index'))
        ->assertSuccessful();
});

test('can list languages', function () {
    $languages = Language::factory()->count(3)->create();

    Livewire::test(ListLanguages::class)
        ->assertCanSeeTableRecords($languages);
});

test('can filter languages by active tab', function () {
    $enabledLanguage = Language::factory()->create(['is_enabled' => true]);
    $disabledLanguage = Language::factory()->create(['is_enabled' => false]);

    Livewire::test(ListLanguages::class)
        ->set('activeTab', 'enabled')
        ->assertCanSeeTableRecords([$enabledLanguage])
        ->assertCanNotSeeTableRecords([$disabledLanguage]);
});

test('language resource uses russian translations when the selected filament locale is russian', function () {
    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);

    $this->withSession(['locale' => 'ru'])
        ->get(LanguageResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSeeText('Языки')
        ->assertSeeText('Все языки')
        ->assertSeeText('Включенные')
        ->assertSeeText('Английский')
        ->assertSeeText('Русский');
});
