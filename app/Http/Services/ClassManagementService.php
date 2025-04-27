<?php
namespace App\Http\Services;

use App\Models\SchoolClassSchoolSubject;
use App\Models\StudentSchoolClassStream;
use DB;
use Illuminate\Http\Request;
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
    public function addStudentToStream($request){
        try{
            DB::beginTransaction();
            StudentSchoolClassStream::updateOrCreate(
                [
                    "student_id" => $request->student, 
                    "school_class_stream_id" => $request->stream
                ],
                [
                    "start_date" => $request->start_date, 
                    "end_date" => $request->end_date,
                    "status" => 'Active'
                ]
            );
            
                DB::commit();
                return response()->json(['sucess' => true, 'message' => 'Student added successfully']);
        }catch(Exception $exception){
            DB::rollBack();
            return response()->json(['sucess' => false, 'message' => 'Student was not added.']);
        }

    }
    public function getClassStudents(){
       $students=  StudentSchoolClassStream::
         join("school_class_streams","school_class_streams.id","=","student_school_class_streams.school_class_stream_id")
         ->join("school_classes","school_classes.id","=","school_class_streams.school_class_id")
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
         return response()->json(['sucess' => false, 'students' =>   $students]);
    }
    public function getSubjectsByClass($classId,Request $request){
     $class_subjects= SchoolClassSchoolSubject::
     join("school_subjects","school_subjects.id","=","school_class_school_subjects.school_subject_id")
     ->join("school_classes","school_classes.id","=","school_class_school_subjects.school_class_id")
     ->where("school_class_id",$classId)
     ->select("school_class_school_subjects.id","school_classes.class_name","school_subjects.subject_name","school_class_school_subjects.created_at as created_at")
     ->paginate($request->perPage?$request->perPage:10);
     return response()->json(['subjects' =>   $class_subjects]);
    }

}