<?php

namespace App\Http\Controllers;

use App\Http\Services\MobileEndpointsUtilityService;
use Illuminate\Http\Request;


class MobileEndpointsUtilityController extends Controller
{
    //
    protected $mobileendpointutilityservice;
    public function __construct(MobileEndpointsUtilityService $mobileendpointutilityservice){
        $this->mobileendpointutilityservice= $mobileendpointutilityservice;
    }
    public function saveUserFCMToken(Request $request){
       $response = $this->mobileendpointutilityservice->saveUserFCMToken($request);
       return $response;
    }
    public function getStudentDiscipline(Request $request){
       $response = $this->mobileendpointutilityservice->getStudentDiscipline($request);
       return $response;
    }
    public function getStudentMedical(Request $request){
       $response = $this->mobileendpointutilityservice->getStudentMedical($request);
       return $response;
    }
    public function getStudentResults(Request $request){
       $response = $this->mobileendpointutilityservice->getStudentResults($request);
       return $response;
    }
   public function getStudentP( $id){
       $response = $this->mobileendpointutilityservice->getStudentP($id);
       return $response;
    }

    
}
