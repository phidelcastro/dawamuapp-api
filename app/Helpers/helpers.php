<?php
use App\Models\Student;
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

if (! function_exists('generateStdAdmission')) {
    function generateStdAdmission($length = 10)
    {
      return DB::transaction(function () {
        $year = now()->format('Y');
        $prefix = "STD-{$year}-";

        $lastStudent = Student::where('student_admission_number', 'LIKE', "{$prefix}%")
            ->orderBy('student_admission_number', 'desc')
            ->lockForUpdate()
            ->first();

        if ($lastStudent && preg_match('/\d+$/', $lastStudent->admission_number, $matches)) {
            $lastNumber = (int)$matches[0];
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    });
    }
}



