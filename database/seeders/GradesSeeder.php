<?php

namespace Database\Seeders;

use App\Models\GradingSystem;
use Illuminate\Database\Seeder;

class GradesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            ['grade' => 'A', 'Comment' => 'Good', 'lower_score' => 80, 'upper_score' => 100],
            ['grade' => 'B', 'Comment' => 'Good', 'lower_score' => 60, 'upper_score' => 79],
            ['grade' => 'C', 'Comment' => 'Good', 'lower_score' => 40, 'upper_score' => 59],
            ['grade' => 'D', 'Comment' => 'Good', 'lower_score' => 0,  'upper_score' => 39],
        ];

        foreach ($grades as $grade) {
            GradingSystem::updateOrCreate(
                ['grade' => $grade['grade']],
                $grade
            );
        }
    }
}
