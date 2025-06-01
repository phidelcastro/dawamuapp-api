<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSubject extends Model
{
    protected $fillable = [
        'subject_name',
        'subject_code',
        'subject_description'
    ];
  public function schoolExamSchoolClassSubjects()
{
    return $this->hasMany(SchoolExamSchoolClassSubject::class);
}

public function teacherSubjects()
{
    return $this->hasMany(TeacherSubject::class, 'school_subject_id');
}
}
