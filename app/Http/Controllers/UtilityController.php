<?php

namespace App\Http\Controllers;

use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use App\Models\SchoolExam;
use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\SchoolClass;
use App\Models\SchoolClassStream;

class UtilityController extends Controller
{
    protected $examservice;
    protected $classmanagementservice;
    public function __construct(ExamService $examService, ClassManagementService $classManagementService){
          $this->examservice=$examService;
          $this->classmanagementservice=$classManagementService;
    }
    
    public function getAllSubjects()
    {
        $subjects = SchoolSubject::all();
        return response()->json(['subjects' => $subjects]);
    }
    public function getSubjectsByClass($classId,Request $request){
        $response =  $this->classmanagementservice->getSubjectsByClass($classId, $request);
        return $response;
    }

    public function getAllClasses(Request $request)
    {
        $classes = SchoolClass::paginate($request->perPage?$request->perPage:2);
        return response()->json(['classes' => $classes]);
    }

    public function getAllStreams(Request $request)
    {
        $streams = SchoolClassStream::paginate($request->perPage?$request->perPage:2);
        return response()->json(['streams' => $streams]);
    }

    public function getStreamsByClass($classId,Request $request)
    {
        $streams = SchoolClassStream::where('school_class_id', $classId)
                                ->paginate($request->perPage ? $request->perPage : 2);

    return response()->json(['streams' => $streams]);
    }
    public function getAllExams(){
        $exams= $this->examservice->getAllExams();
        return response()->json(['exams' => $exams],201);

    }
    public function getExamsByClass($classId,Request $request){
        $exams= $this->examservice->getExamsByClass($classId,$request);
        return $exams;
    }
    public function getStudentsByClass($classId){
        $exams= $this->classmanagementservice->getClassStudents($classId);
        return response()->json(['exams' => $exams],201);
    }
public function getExamEligibleStudentsByClass($classId,$examId){

    $exams= $this->examservice->getExamEligibleStudentsByClass($classId,$examId);
    return response()->json(['exams' => $exams],201);

}
public function recordExamSubjectResults(){


}
public function getExamSubjectsByClassAndExam(){
    $exams= $this->examservice->getExamSubjectsByClassAndExam($classId,$examId);
    return response()->json(['exams' => $exams],201);
}

 
}
