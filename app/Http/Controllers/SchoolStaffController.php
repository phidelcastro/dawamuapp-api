<?php

namespace App\Http\Controllers;

use App\Http\Services\SchoolStaffService;
use Illuminate\Http\Request;

class SchoolStaffController extends Controller
{
      protected $utilityservice;
public function __construct(SchoolStaffService $schoolStaffService){
  $this->schoolStaffService=$schoolStaffService;
}
    public function registerStaff(Request $request){
 $reponse = $this->schoolStaffService->registerStaff($request);
 return $reponse;
    }
}
