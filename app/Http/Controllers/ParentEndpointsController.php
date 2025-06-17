<?php

namespace App\Http\Controllers;

use App\Http\Services\MobileEndpointsUtilityService;
use App\Http\Services\ParentEndpointsService;
use Illuminate\Http\Request;


class ParentEndpointsController extends Controller
{
 
    protected $parentEndpointsService;
    public function __construct(ParentEndpointsService $parentEndpointsService){
        $this->parentEndpointsService= $parentEndpointsService;
    }
    public function getMyStudents(Request $request){
       $response= $this->parentEndpointsService->getMyStudents($request);
       return $response;
    }
    public function getEventInvites(Request $request){
        $response= $this->parentEndpointsService->getMyEventInvites($request);
       return $response;
    }
        public function getMyMessages(Request $request){
        $response= $this->parentEndpointsService->getMyMessages($request);
       return $response;
    }
            public function confirmEventAttendance(Request $request){
        $response= $this->parentEndpointsService->confirmEventAttendance($request);
       return $response;
    }

                public function editEventComment(Request $request){
        $response= $this->parentEndpointsService->editEventComment($request);
       return $response;
    }
    

    
    
}
