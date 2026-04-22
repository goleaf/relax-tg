@php
    $items = [
        [
            'active' => request()->routeIs('filament.admin.pages.dashboard'),
            'label' => \App\Filament\Pages\Dashboard::getNavigationLabel(),
            'slug' => 'dashboard',
            'url' => \App\Filament\Pages\Dashboard::getUrl(),
        ],
        [
            'active' => request()->routeIs('filament.admin.resources.languages.*'),
            'label' => \App\Filament\Resources\Languages\LanguageResource::getNavigationLabel(),
            'slug' => 'languages',
            'url' => \App\Filament\Resources\Languages\LanguageResource::getUrl('index'),
        ],
    ];
@endphp

<ul class="fi-topbar-nav-groups" data-test-topbar-primary-navigation>
    @foreach ($items as $item)
        <x-filament-panels::topbar.item
            :active="$item['active']"
            :url="$item['url']"
            :attributes="new \Illuminate\View\ComponentAttributeBag([
                'data-test-topbar-link' => $item['slug'],
            ])"
        >
            {{ $item['label'] }}
        </x-filament-panels::topbar.item>
    @endforeach
</ul>
