<?php

namespace App\Providers;

use App\Models\ExperienceLevel;
use App\Models\FocusProblem;
use App\Models\Language;
use App\Models\MeditationType;
use App\Models\ModuleChoice;
use App\Models\Practice;
use App\Observers\PerformanceCacheObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
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
        Model::preventLazyLoading(! $this->app->isProduction());

        RateLimiter::for('telegram-bot-updates', fn (): Limit => Limit::perMinute(300)->by('telegram-bot-updates'));

        $this->registerPerformanceCacheObservers();

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

    private function registerPerformanceCacheObservers(): void
    {
        $observer = PerformanceCacheObserver::class;

        Practice::observe($observer);
        Language::observe($observer);
        FocusProblem::observe($observer);
        ExperienceLevel::observe($observer);
        ModuleChoice::observe($observer);
        MeditationType::observe($observer);
    }
}
