<?php

namespace App\Providers;

use App\Models\Language;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Schema;
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
            // Dynamically load enabled languages from the database.
            // Falls back to ['en'] if the table doesn't exist yet (e.g. fresh install).
            $locales = Schema::hasTable('languages')
                ? Language::enabled()->pluck('code')->toArray()
                : ['en'];

            $switch->locales($locales);
        });
    }
}
