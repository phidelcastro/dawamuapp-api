<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolTerm; // ✅ Import the model

class SchoolTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = ['Term One', 'Term Two', 'Term Three', 'Term Four'];

        foreach ($terms as $term) {
            if (!SchoolTerm::where('term_label', $term)->exists()) {
                SchoolTerm::create(['term_label' => $term]);
            }
        }
    }
}
