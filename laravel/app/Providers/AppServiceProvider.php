<?php

namespace App\Providers;

use App\Models\Language;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(Language::supportedInterfaceLocales())
                ->labels(self::filamentInterfaceLabels())
                ->displayLocale(fn (): string => app()->getLocale());
        });
    }

    /**
     * @return array<string, string>
     */
    private static function filamentInterfaceLabels(): array
    {
        return collect(Language::supportedInterfaceLocales())
            ->mapWithKeys(fn (string $locale): array => [
                $locale => Language::nativeName($locale, Language::displayName($locale, $locale)),
            ])
            ->all();
    }
}
