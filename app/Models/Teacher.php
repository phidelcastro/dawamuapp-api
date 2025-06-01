<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'level_of_education',
        'tsc_number',
        'years_of_experience_prior_employment',
        'date_of_employment',
    ];
    protected $appends = ['teacher_stream_subject_assignments'];
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class)->with(relations: 'teacherStreamSubjects');
    }
public function getTeacherStreamSubjectAssignmentsAttribute()
{
    $assignments = [];

    foreach ($this->teacherSubjects as $teacherSubject) {
        foreach ($teacherSubject->teacherStreamSubjects as $streamSubject) {
            $assignments[] = [
                'teacher_subject_id' => $streamSubject->teacher_subject_id,
                'school_class_stream_id' => $streamSubject->school_class_stream_id,
                'is_stream_class_teacher' => $streamSubject->is_stream_class_teacher,
                'subject_name' => $streamSubject->schoolSubject->subject_name,
                'start_date' => $streamSubject->start_date,
                'end_date' => $streamSubject->end_date,
                'current_status' => $streamSubject->current_status,
                'class_name' => $this->getClassDetails( $streamSubject->school_class_stream_id,),
              
            ];
        }
    }

    return $assignments;
}
public function getClassDetails($stream){
  $class= SchoolClassStream::join("school_classes","school_classes.id","=","school_class_streams.school_class_id")
   ->where("school_class_id",$stream)
   ->first();
   return $class;

}

}
