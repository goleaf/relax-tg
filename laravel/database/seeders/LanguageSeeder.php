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

        foreach ($languages as $code => $name) {
            Language::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'native_name' => $nativeNames[$code] ?? $name,
                    'is_enabled' => in_array($code, self::DEFAULT_ENABLED_CODES, true),
                ],
            );
        }

        Language::query()
            ->whereNotIn('code', self::DEFAULT_ENABLED_CODES)
            ->update(['is_enabled' => false]);
    }
}
