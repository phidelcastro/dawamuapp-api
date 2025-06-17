<?php

namespace App\Http\Controllers;

use App\Http\Services\MobileEndpointsUtilityService;
use App\Http\Services\ParentEndpointsService;
use App\Http\Services\StaffEndpointsService;
use App\Http\Services\StudentEndpointsService;
use Illuminate\Http\Request;


class StudentEndPointsController extends Controller
{
 
    protected $studentEndpointsService;
    public function __construct(StudentEndpointsService $studentEndpointsService){
        $this->studentEndpointsService= $studentEndpointsService;
    }
 
}
