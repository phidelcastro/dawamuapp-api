<?php

namespace App\Http\Controllers;

use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
      protected $eaxamservice;
    protected $classManagentService;
    public function __construct(ExamService $eaxamservice, ClassManagementService $classManagentService){
           $this->eaxamservice=$eaxamservice;
           $this->classManagentService=$classManagentService;
    }
    public function getLatestStudentExam(Request $request){
       $response =  $this->eaxamservice->getLatestExamForAStudent($request);
       return $response;
    }
 public function getPreviousExamForAStudent(Request $request){
       $response =  $this->eaxamservice->getPreviousExamForAStudent($request);
       return $response;
    }

     public function getLatestExamDetails(Request $request){
       $response =  $this->eaxamservice->getLatestExamDetails($request);
       return $response;
    }

    
}
