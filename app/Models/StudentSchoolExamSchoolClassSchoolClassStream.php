<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class StudentSchoolExamSchoolClassSchoolClassStream extends Model
{
    //

    protected $fillable = [
        'school_exam_school_class_subject_id',
        'school_exam_school_class_school_class_streams_id',
        'score',
        'student_id',
        'percentage_score',
        'grade_id',
    ];
    protected $with = ['gradeDetails',
    'schoolExamSchoolClassSubject.schoolClassSchoolSubject.subjectDetails',
    'schoolExamSchoolClassSchoolClassStream.schoolClassStream.schoolClass'
];
    public function studentDetails()
    {
        return $this->belongsTo(Student::class, "student_id");
    }
    public function gradeDetails()
    {
        return $this->belongsTo(GradingSystem::class, "grade_id");
    }
    public function schoolExamSchoolClassSubject(){
        return $this->belongsTo(SchoolExamSchoolClassSubject::class,"school_exam_school_class_subject_id");
    }
    public function schoolExamSchoolClassSchoolClassStream(){
        return $this->belongsTo(SchoolExamSchoolClassSchoolClassStream::class,"school_exam_school_class_school_class_streams_id");
    }
    
}
