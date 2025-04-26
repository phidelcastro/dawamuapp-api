<?php
namespace App\Http\Services;

use App\Models\SchoolClassSchoolSubject;
use App\Models\StudentSchoolClassStream;
use DB;
class ClassManagementService
{
    public function addSubjectsToClass($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->subjects as $subject) {
                SchoolClassSchoolSubject::updateOrCreate([
                    'school_class_id' => $subject['class_id'],
                    'school_subject_id' => $subject['subject_id']
                ]);
            }
            DB::commit();
            return response()->json(['sucess' => true, 'message' => 'Subject added successfully']);
        } catch (Exception $exception) {

            DB::rollBack();
            return response()->json(['sucess' => false, 'message' => 'Subject were not added.']);
        }
    }
    public function addStudentToClass(){
        
    }
    public function getClassStudents(){
       $students=  StudentSchoolClassStream::
         join("school_classes","school_classes.id","=","student_school_class_streams.school_class_id")
         ->join("student_school_class_streams","student_school_class_streams.student_id","=","students.id")
         ->join("students","students.user_id","=","users.id")
         ->get();
         return response()->json(['sucess' => false, 'students' =>   $students]);
    }

}