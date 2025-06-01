<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExamSchoolClassSubject extends Model
{
    protected $fillable = [
        'school_exam_school_class_id',
        'school_class_school_subject_id',
        'exam_paper_link',
        'total_score',
        'status',
    ];
    public function SchoolExamSchoolClass(){
        return $this->belongsTo(SchoolExamSchoolClass::class,"school_exam_school_class_id");
    }
    public function schoolClassSchoolSubject(){
        return $this->belongsTo(SchoolClassSchoolSubject::class,"school_class_school_subject_id");
    }
}
