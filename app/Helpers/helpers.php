<?php
use Illuminate\Support\Str;

use App\Models\SchoolExamSchoolClass;

if (! function_exists('getSchoolExamSchoolClassId')) {
    function getSchoolExamSchoolClassId($examId, $classId)
    {
        $record = SchoolExamSchoolClass::firstOrCreate([
            'school_exam_id' => $examId,
            'school_class_id' => $classId,
        ]);

        return $record->id;
    }
}

if (! function_exists('generateStudentAdmissionNumber')) {
    function generateStudentAdmissionNumber($examId, $classId)
    {
     
    }
}

if (! function_exists('generateRandomPassword')) {
    function generateRandomPassword($length = 10)
    {
        $base = Str::random($length - 2); 
        $symbols = '!@#$%^&*';
        return $base . $symbols[random_int(0, strlen($symbols) - 1)] . random_int(0, 9);
    }
}