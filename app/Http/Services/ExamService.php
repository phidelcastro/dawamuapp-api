<?php
namespace App\Http\Services;

use App\Http\Requests\CreateExamRequest;
use App\Models\SchoolClassExam;
use App\Models\SchoolClassStream;
use App\Models\SchoolExam;
use App\Models\SchoolExamSchoolClass;
use App\Models\SchoolExamSchoolClassSchoolClassStream;
use App\Models\SchoolExamSchoolClassSubject;
use App\Models\StudentSchoolClassStream;
use DB;
use Exception;

class ExamService
{
    public function createExam(CreateExamRequest $request)
    {
        try {
            DB::beginTransaction();

            $created_exam = SchoolExam::create([
                'exam_label' => $request->exam_label,
                'Year' => $request->Year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'exam_status' => 'Active',
                'note' => $request->note,
                'target' => $request->target,
                'exam_type' => $request->exam_type,
                'school_term' => $request->school_term,
            ]);
            // Attach classes
            foreach ($request->targetClasses as $targetClass) {
                $schoolExamSchoolClass = SchoolExamSchoolClass::create([
                    'school_exam_id' => $created_exam->id,
                    'school_class_id' => $targetClass,
                ]);

                // Attach streams for the class
                $allclassstreams = SchoolClassStream::where("school_class_id", $targetClass)
                    ->whereIn("id", $request->targetStreams)
                    ->pluck('id');

                foreach ($allclassstreams as $targetStream) {
                    SchoolExamSchoolClassSchoolClassStream::create([
                        'school_exam_school_class_id' => $schoolExamSchoolClass->id,
                        'school_class_stream_id' => $targetStream,
                        'status' => 'Active',
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Exam created.',
                'data' => $created_exam,
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Exam has not been created.',
                'error' => $exception->getMessage()
            ]);
        }
    }
  
    public function addSubjectsToExams($request)
    {
        try {
            DB::beginTransaction();

            $exampfound = SchoolExam::where("id", $request->exam)->first();
            if ($exampfound) {
                foreach ($request->subjects as $subject) {
                    // You will need to get the school_exam_school_class_id
                    // Probably you are saving somewhere the relation between exam and class
                    $school_exam_school_class_id = getSchoolExamSchoolClassId($request->exam, $subject['class']); 
                    // ^^^ You need a function like this based on your database design
                    
                    foreach ($subject['classsubjects'] as $classsubject) {
                        SchoolExamSchoolClassSubject::updateOrCreate(
                            [
                                'school_exam_school_class_id' => $school_exam_school_class_id,
                                'school_class_school_subject_id' => $classsubject['school_class_school_subject_id'],
                            ],
                            [
                                'exam_paper_link' => $classsubject['exam_paper_link'],
                                'total_score' => $classsubject['total_score'],
                                'status' => $classsubject['status'],
                            ]
                        );
                    }
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Exam subjects added successfully.',

            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Exam subjects were not added successfully.',
                'error'=>$exception->getMessage()

            ]);
        }
    }
    public function getAllExams()
    {
        $exams = SchoolExam::with([
            'SchoolExamSchoolClass.SchoolClassDetails',
            'SchoolExamSchoolClass.subjects.subjectDetails',
             'SchoolExamSchoolClass.streams'
            ])->get();
        return response()->json(['exams' => $exams]);

    }
    public function getExamsByClass($classId,$request)
    {
        $exams = SchoolExam::whereHas('SchoolExamSchoolClass', function ($query)use($classId) {           
            $query->where('school_class_id', $classId);
        })
        ->with([
            'SchoolExamSchoolClass.SchoolClassDetails',
            'SchoolExamSchoolClass.subjects.subjectDetails',
            'SchoolExamSchoolClass.streams'
        ])
        ->paginate($request->perPage?$request->perPage:10);        
        return response()->json(['exams' => $exams]);

    }
public function getExamEligibleStudentsByClass($classId,$examId){
    $students=  StudentSchoolClassStream::
    join("school_class_streams","school_class_streams.id","=","student_school_class_streams.school_class_stream_id")
    ->join("school_classes","school_classes.id","=","school_class_streams.school_class_id")
    ->join("school_exam_school_classes","school_exam_school_classes.school_class_id","=","school_classes.id")
    ->join("students","students.id","=","student_school_class_streams.student_id")
    ->join("users","users.id","=","students.user_id")
    ->where("school_classes.id",$classId)
    ->where("school_exam_school_classes.school_exam_id",$examId)
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
   return response()->json(['exams' => $students]);
}
public function getExamSubjectsByClassAndExam($classId,$examId){

}

}
