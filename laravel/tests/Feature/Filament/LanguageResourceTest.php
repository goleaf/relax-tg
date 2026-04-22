<?php

use App\Filament\Resources\Languages\LanguageResource;
use App\Filament\Resources\Languages\Pages\ListLanguages;
use App\Models\Language;
use App\Models\User;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render languages list page', function () {
    $this->get(LanguageResource::getUrl('index'))
        ->assertSuccessful();
});

test('language resource registers only the index page', function () {
    expect(array_keys(LanguageResource::getPages()))->toBe(['index']);
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
    Language::query()->create(['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'is_enabled' => true]);
    Language::query()->create(['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'is_enabled' => true]);

    $this->withSession(['locale' => 'ru'])
        ->get(LanguageResource::getUrl('index'))
        ->assertSuccessful()
        ->assertSeeText('Языки')
        ->assertSeeText('Все языки')
        ->assertSeeText('Включенные')
        ->assertSeeText('Название')
        ->assertSeeText('Родное название')
        ->assertSeeText('English')
        ->assertSeeText('Russian')
        ->assertSeeText('French')
        ->assertSeeText('Русский')
        ->assertSeeText('Français');
});

test('filament interface switch stays limited to english and russian', function () {
    Language::query()->create(['code' => 'en', 'name' => 'English', 'is_enabled' => true]);
    Language::query()->create(['code' => 'ru', 'name' => 'Russian', 'is_enabled' => true]);
    Language::query()->create(['code' => 'fr', 'name' => 'French', 'is_enabled' => true]);

    expect(LanguageSwitch::make()->getLocales())->toBe(['en', 'ru']);
});
