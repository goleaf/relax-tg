<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    private const DEFAULT_ENABLED_CODES = ['en', 'ru'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = config('languages');
        $nativeNames = config('language_native_names');

        if (! is_array($languages)) {
            return;
        }

        $nativeNames = is_array($nativeNames) ? $nativeNames : [];

        foreach ($languages as $code => $name) {
            if (! is_string($code) || ! is_string($name)) {
                continue;
            }

            $nativeName = $nativeNames[$code] ?? $name;

            Language::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'native_name' => is_string($nativeName) ? $nativeName : $name,
                    'is_enabled' => in_array($code, self::DEFAULT_ENABLED_CODES, true),
                ],
            );
        }

        Language::query()
            ->whereNotIn('code', self::DEFAULT_ENABLED_CODES)
            ->update(['is_enabled' => false]);
    }
}
