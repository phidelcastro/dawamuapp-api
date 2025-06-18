<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolSubject;

class KenyanHighSchoolSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Core subjects
            ['subject_name' => 'English', 'subject_code' => 'ENG', 'subject_description' => 'English language and literature'],
            ['subject_name' => 'Kiswahili', 'subject_code' => 'KISW', 'subject_description' => 'Kiswahili language studies'],
            ['subject_name' => 'Mathematics', 'subject_code' => 'MATH', 'subject_description' => 'Mathematics concepts and applications'],
            ['subject_name' => 'Biology', 'subject_code' => 'BIO', 'subject_description' => 'Life sciences'],
            ['subject_name' => 'Physics', 'subject_code' => 'PHY', 'subject_description' => 'Physical sciences'],
            ['subject_name' => 'Chemistry', 'subject_code' => 'CHEM', 'subject_description' => 'Chemical sciences'],
            ['subject_name' => 'Geography', 'subject_code' => 'GEO', 'subject_description' => 'Physical and human geography'],
            ['subject_name' => 'History and Government', 'subject_code' => 'HIST', 'subject_description' => 'Historical and governance studies'],
            ['subject_name' => 'CRE (Christian Religious Education)', 'subject_code' => 'CRE', 'subject_description' => 'Christian doctrine and teachings'],
            ['subject_name' => 'IRE (Islamic Religious Education)', 'subject_code' => 'IRE', 'subject_description' => 'Islamic doctrine and teachings'],
            ['subject_name' => 'HRE (Hindu Religious Education)', 'subject_code' => 'HRE', 'subject_description' => 'Hindu doctrine and teachings'],

            // Technical & optional
            ['subject_name' => 'Business Studies', 'subject_code' => 'BST', 'subject_description' => 'Entrepreneurship, finance and commerce'],
            ['subject_name' => 'Agriculture', 'subject_code' => 'AGRI', 'subject_description' => 'Farming and agricultural science'],
            ['subject_name' => 'Computer Studies', 'subject_code' => 'COMP', 'subject_description' => 'ICT and computing'],
            ['subject_name' => 'Home Science', 'subject_code' => 'HOME', 'subject_description' => 'Family and consumer science'],
            ['subject_name' => 'Art and Design', 'subject_code' => 'ART', 'subject_description' => 'Creative arts and design'],
            ['subject_name' => 'Music', 'subject_code' => 'MUS', 'subject_description' => 'Music theory and performance'],
            ['subject_name' => 'French', 'subject_code' => 'FRE', 'subject_description' => 'French language studies'],
            ['subject_name' => 'German', 'subject_code' => 'GER', 'subject_description' => 'German language studies'],
            ['subject_name' => 'Arabic', 'subject_code' => 'ARB', 'subject_description' => 'Arabic language studies'],
        ];

        foreach ($subjects as $subject) {
            SchoolSubject::updateOrCreate(
                ['subject_code' => $subject['subject_code']],
                $subject
            );
        }
    }
}

