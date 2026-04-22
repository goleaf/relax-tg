<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = config('languages');

        foreach ($languages as $code => $name) {
            Language::firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_enabled' => false]
            );
        }

        // Enable English and Russian by default
        Language::whereIn('code', ['en', 'ru'])->update(['is_enabled' => true]);
    }
}
