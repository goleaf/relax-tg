<?php

namespace Database\Seeders;

use App\Models\Practice;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 practices for each of the 29 days to ensure content across all submenus.
        for ($day = 1; $day <= 29; $day++) {
            Practice::factory(2)->create([
                'day' => $day,
            ]);
        }
    }
}
