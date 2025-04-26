<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSchoolExamSchoolClassSchoolClassStream extends Model
{
    //

   protected $fillable = [
    'school_exam_school_class_subject_id',
    'school_exam_school_class_school_class_streams_id',
    'score',
    'percentage_score',
    'grade_id',
];
}
