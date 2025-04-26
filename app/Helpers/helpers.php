<?php

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
