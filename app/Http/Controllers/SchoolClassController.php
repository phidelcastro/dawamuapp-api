<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExamRequest;
use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use App\Http\Services\TeacherService;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
class SchoolClassController extends Controller
{
    //
    protected $eaxamservice;
    protected $classManagentService;
    protected $teacherservice;
    public function __construct(ExamService $eaxamservice, ClassManagementService $classManagentService, TeacherService $teacherService){
           $this->eaxamservice=$eaxamservice;
           $this->classManagentService=$classManagentService;
           $this->teacherservice=$teacherService;
    }
    public function createClass(Request $request)
    {
        $class = SchoolClass::create($request->all());
        return response()->json($class);
    }
    public function createExam(Request $request){
        // return response()->json($request->examinfo);
         $reponse = $this->eaxamservice->createExam($request);
        return $reponse;
    }
    public function addSubjectsToExams(Request $request){
        $reponse = $this->eaxamservice->addSubjectsToExams($request);
        return response()->json([$reponse]);
    }
    public function addSubjectsToClass(Request $request){

     $response =  $this->classManagentService->addSubjectsToClass($request);
     return response()->json([$response]);

    }
    public function addStudentToStream(Request $request){
        $response =  $this->classManagentService->addStudentToStream($request);
        return response()->json([$response]);
    }
    public function updateStudentExamResult(Request $request){
        $response =  $this->eaxamservice->updateStudentExamResult($request);
        return response()->json([$response]);
    }
    public function getSingleStudentResults(Request $request){
          $response =  $this->eaxamservice->getSingleStudentResults($request);
        return response()->json([$response]);
    }
    public function registerStudentAndAssignStream(Request $request){
        $response =  $this->classManagentService->registerStudentAndAssignStream($request);
        return $response;
    }
    public function updateStudentAndStream(Request $request, $studentId){
         $response =  $this->classManagentService->updateStudentAndStream($request,$studentId);
        return $response; 
    }
    public function registerTeacher(Request $request){
                 $response =  $this->teacherservice->registerTeacherByAdmin($request);
        return $response; 
    }
    public function registerTeacherStreamSubjects(Request $request){
               $response =  $this->teacherservice->registerTeacherStreamSubjects($request);
        return $response; 
    }
    public function detachTeacherStreamSubjects(Request $request){
                 $response =  $this->teacherservice->detachTeacherStreamSubjects($request);
        return $response; 
    }
}
