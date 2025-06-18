<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    //
        protected $fillable = [
        'school_subject_id',
        'teacher_id',
        'is_main',
    ];
public function teacher()
{
    return $this->belongsTo(Teacher::class, 'teacher_id');
}

public function schoolSubject()
{
    return $this->belongsTo(SchoolSubject::class, 'school_subject_id');
}

public function teacherStreamSubjects()
{
    return $this->hasMany(SchoolClassStreamTeacherSubject::class, 'teacher_subject_id');
}

}
