<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_admission_number',
        'date_of_admission',
        'status',
        'guardian_id',
        'admitted_on_school_class_id'
    ];
    protected $appends = ['full_name', 'latest_exam', 'previous_exam'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function studentSchoolExamSchoolClassSchoolClassStream()
    {
        return $this->hasMany(StudentSchoolExamSchoolClassSchoolClassStream::class);
    }
    public function StudentSchoolClassStream()
    {
        return $this->hasMany(StudentSchoolClassStream::class);
    }
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
    public function getLatestExamAttribute($data)
    {
        $subjects =
            DB::table('student_school_exam_school_class_school_class_streams')
                ->join('school_exam_school_class_subjects', 'school_exam_school_class_subjects.id', '=', 'student_school_exam_school_class_school_class_streams.school_exam_school_class_subject_id')
                ->join('school_exam_school_classes', 'school_exam_school_classes.id', '=', 'school_exam_school_class_subjects.school_exam_school_class_id')
                ->where('student_id', $this->id)
                ->select(
                    'school_exam_school_classes.school_exam_id',
                    'school_exam_school_classes.school_class_id'
                )
                ->distinct()
                ->take(1)
                ->orderBy("school_exam_school_classes.school_exam_id", "DESC")

                ->first();

        return $subjects ?: (object) [
            'school_exam_id' => null,
            'school_class_id' => null,
        ];
    }
    public function getPreviousExamAttribute($data)
    {
        $secondExam = DB::table('student_school_exam_school_class_school_class_streams')
            ->join('school_exam_school_class_subjects', 'school_exam_school_class_subjects.id', '=', 'student_school_exam_school_class_school_class_streams.school_exam_school_class_subject_id')
            ->join('school_exam_school_classes', 'school_exam_school_classes.id', '=', 'school_exam_school_class_subjects.school_exam_school_class_id')
            ->where('student_id', $this->id)
            ->select(
                'school_exam_school_classes.school_exam_id',
                'school_exam_school_classes.school_class_id'
            )
            ->distinct()
            ->orderBy('school_exam_school_classes.school_exam_id', 'DESC')
            ->skip(1)
            ->take(1)
            ->first();
        return $secondExam ?: (object) [
            'school_exam_id' => null,
            'school_class_id' => null,
        ];
        
    }
    public function otherContacts()
    {
        return $this->hasMany(OtherContact::class);
    }
    public function getFullNameAttribute()
    {
        return "{$this->user->first_name} {$this->user->middle_name} {$this->user->last_name}";
    }


}
