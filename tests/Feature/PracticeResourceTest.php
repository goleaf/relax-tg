<?php

use App\Filament\Resources\Practices\Pages\CreatePractice;
use App\Filament\Resources\Practices\Pages\EditPractice;
use App\Filament\Resources\Practices\Pages\ListPractices;
use App\Models\Practice;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('can render practice list page', function () {
    Livewire::test(ListPractices::class)->assertSuccessful();
});

it('can render practice create page', function () {
    Livewire::test(CreatePractice::class)->assertSuccessful();
});

it('can render practice edit page', function () {
    $practice = Practice::factory()->create();

    Livewire::test(EditPractice::class, [
        'record' => $practice->getKey(),
    ])->assertSuccessful();
});
