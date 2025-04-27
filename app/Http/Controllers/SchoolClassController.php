<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExamRequest;
use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
class SchoolClassController extends Controller
{
    //
    protected $eaxamservice;
    protected $classManagentService;
    public function __construct(ExamService $eaxamservice, ClassManagementService $classManagentService){
           $this->eaxamservice=$eaxamservice;
           $this->classManagentService=$classManagentService;
    }
    public function createClass(Request $request)
    {
        $class = SchoolClass::create($request->all());
        return response()->json($class);
    }
    public function createExam(CreateExamRequest $request){
         $reponse = $this->eaxamservice->createExam($request);
        return response()->json([$reponse]);
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
}
