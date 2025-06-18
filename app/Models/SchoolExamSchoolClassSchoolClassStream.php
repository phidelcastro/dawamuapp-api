<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExamSchoolClassSchoolClassStream extends Model
{
    //
    protected $fillable = [
        'school_exam_school_class_id',
        'school_class_stream_id',
        'status',
    ];
    // protected $appends=['students_results','eligible_exam_students'];
    public function schoolClassStream(){
        return $this->belongsTo(SchoolClassStream::class,"school_class_stream_id");
    }
    public function examClass(){
        return $this->belongsTo(SchoolExamSchoolClass::class,'school_exam_school_class_id');
    }
    
    public function studentSchoolExamSchoolClassSchoolClassStream(){
        $this->hasMany(StudentSchoolExamSchoolClassSchoolClassStream::class);
    }
    public function getStudentsResultsAttribute(){
        $students=  StudentSchoolExamSchoolClassSchoolClassStream::
        join("school_exam_school_class_school_class_streams","school_exam_school_class_school_class_streams.id","=","student_school_exam_school_class_school_class_streams.school_exam_school_class_school_class_streams_id")
        ->join("school_class_streams","school_class_streams.id","=","school_exam_school_class_school_class_streams.school_class_stream_id")
        ->join("school_classes","school_classes.id","=","school_class_streams.school_class_id")
        ->join("student_school_class_streams","student_school_class_streams.school_class_stream_id","=","school_class_streams.id")
        ->join("students","students.id","=","student_school_class_streams.student_id")
        ->join("users","users.id","=","students.user_id")  
        ->select('students.id AS student_id',
           'students.student_admission_number',
           'students.user_id',
           'students.admitted_on_school_class_id',
           'students.date_of_admission',
           'students.status AS student_account_status',
           'first_name',
           'middle_name',
           'last_name',
           'date_of_birth',
           'account_status AS user_account_status',
           'gender',
           'phone_number',
           'closed_at',
           'email'
       )
       ->get();
       return $students;
    }
    public function getEligibleExamStudentsAttribute(){
        $students=  StudentSchoolClassStream::
        join("school_class_streams","school_class_streams.id","=","student_school_class_streams.school_class_stream_id")
        ->join("school_classes","school_classes.id","=","school_class_streams.school_class_id")
        ->join("students","students.id","=","student_school_class_streams.student_id")
        ->join("users","users.id","=","students.user_id")
        ->where("school_class_streams.id",$this->school_class_stream_id)
        ->select('students.id AS student_id',
           'students.student_admission_number',
           'students.user_id',
           'students.admitted_on_school_class_id',
           'students.date_of_admission',
           'students.status AS student_account_status',
           'first_name',
           'middle_name',
           'last_name',
           'date_of_birth',
           'account_status AS user_account_status',
           'gender',
           'phone_number',
           'closed_at',
           'email'
       )
       ->get();
        return response()->json(['sucess' => false, 'students' =>   $students]);
    }
}
