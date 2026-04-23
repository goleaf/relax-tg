<?php

use App\Models\User;

dataset('protected_admin_routes', [
    'dashboard' => ['/'],
    'practices index' => ['/practices'],
    'focus problems index' => ['/focus-problems'],
    'experience levels index' => ['/experience-levels'],
    'module choices index' => ['/module-choices'],
    'meditation types index' => ['/meditation-types'],
    'languages index' => ['/languages'],
]);

test('guests are redirected to the admin login page for protected admin routes', function (string $url) {
    $this->get($url)
        ->assertRedirectToRoute('filament.admin.auth.login');
})->with('protected_admin_routes');

test('authenticated users can access protected admin routes', function (string $url) {
    $this->actingAs(User::factory()->create())
        ->get($url)
        ->assertSuccessful();
})->with('protected_admin_routes');
