<?php

namespace App\Http\Controllers;

use App\Http\Services\MobileEndpointsUtilityService;
use App\Http\Services\ParentEndpointsService;
use App\Http\Services\StaffEndpointsService;
use Illuminate\Http\Request;


class StaffEndpointsController extends Controller
{
 
    protected $staffEndpointsService;
    public function __construct(StaffEndpointsService $staffEndpointsService){
        $this->staffEndpointsService= $staffEndpointsService;
    }
 
}
