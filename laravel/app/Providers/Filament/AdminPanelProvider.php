<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\ExperienceLevelResource;
use App\Filament\Resources\FocusProblemResource;
use App\Filament\Resources\Languages\LanguageResource;
use App\Filament\Resources\MeditationTypeResource;
use App\Filament\Resources\ModuleChoiceResource;
use App\Filament\Resources\Practices\PracticeResource;
use BezhanSalleh\LanguageSwitch\Http\Middleware\SwitchLanguageLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use JeffersonGoncalves\Filament\Topbar\TopbarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->navigation(fn (): NavigationBuilder => $this->navigationBuilder())
            ->userMenu(false)
            ->darkMode(false)
            ->maxContentWidth(Width::Full)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->plugins([
                TopbarPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->middleware([
                SwitchLanguageLocale::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function navigationBuilder(): NavigationBuilder
    {
        return (new NavigationBuilder)
            ->groups([
                NavigationGroup::make()->items([
                    ...Dashboard::getNavigationItems(),
                    ...PracticeResource::getNavigationItems(),
                ]),
                NavigationGroup::make(__('admin.navigation_groups.categories'))
                    ->extraTopbarAttributes([
                        'data-test-topbar-link' => 'categories',
                        'data-topbar-trigger-icon' => Heroicon::OutlinedFolder->value,
                    ])
                    ->items($this->categoryNavigationItems()),
                NavigationGroup::make()->items(LanguageResource::getNavigationItems()),
            ]);
    }

    /**
     * @return array<NavigationItem>
     */
    private function categoryNavigationItems(): array
    {
        return [
            ...FocusProblemResource::getNavigationItems(),
            ...ExperienceLevelResource::getNavigationItems(),
            ...ModuleChoiceResource::getNavigationItems(),
            ...MeditationTypeResource::getNavigationItems(),
        ];
    }
}
