<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Language>
 */
class LanguageFactory extends Factory
{
    public function definition(): array
    {
        $code = $this->faker->unique()->languageCode();
        $name = $this->faker->unique()->word();

        return [
            'code' => $code,
            'name' => $name,
            'native_name' => Language::nativeName($code, $name),
            'is_enabled' => $this->faker->boolean(),
        ];
    }
}
