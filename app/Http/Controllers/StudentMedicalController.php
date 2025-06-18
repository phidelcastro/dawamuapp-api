<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\StudentMedicalService;

class StudentMedicalController extends Controller
{
    //
    protected $studentMedicalService;
    public function __construct(StudentMedicalService $studentMedicalService)
    {
      $this->studentMedicalService = $studentMedicalService;
    }

    public function saveStudentMedicalRecords(Request $request){
      $response =   $this->studentMedicalService->saveStudentMedicalRecords($request);
      return $response;
    }
    public function updateStudentMedicalRecords(Request $request,$id){
        $response =   $this->studentMedicalService->updateStudentMedicalRecords($request,$id);
        return $response;
      }

    
    public function getMedicalRecords(Request $request){
        $response =   $this->studentMedicalService->getMedicalRecords($request);
        return $response;
    }

}
