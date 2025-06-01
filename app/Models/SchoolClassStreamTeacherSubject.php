<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClassStreamTeacherSubject extends Model
{
    //
    protected $fillable = [
    'teacher_subject_id',
    'school_class_stream_id',
    'is_stream_class_teacher',
    'start_date',
    'end_date',
    'current_status',
];
public function teacherSubject()
{
    return $this->belongsTo(TeacherSubject::class, 'teacher_subject_id');
}
public function streamDetails(){
    return $this->belongsTo(SchoolClassStream::class);
}
// public function getSchoolSubjectAttribute()
// {
//     return $this->teacherSubject?->schoolSubject;
// }
    public function schoolSubject()
{
    return $this->hasOneThrough(
        SchoolSubject::class,
        TeacherSubject::class,
        'id',                 
        'id',               
        'teacher_subject_id',  
        'school_subject_id'    
    );
}
}
