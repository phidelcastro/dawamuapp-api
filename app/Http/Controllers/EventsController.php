<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\EventsService;

class EventsController extends Controller
{
    //
    protected $EventsService;
    public function __construct(EventsService $EventsService)
    {
      $this->EventsService = $EventsService;
    }

    public function saveEventRecord(Request $request){
      $response =   $this->EventsService->saveEventRecord($request);
      return $response;
    }
    public function updateEventRecord(Request $request,$id){
        $response =   $this->EventsService->updateEventRecord($request,$id);
        return $response;
      }

    
    public function getEventsRecords(Request $request){
        $response =   $this->EventsService->getEventsRecords($request);
        return $response;
    }
    public function getEventRecipients($id){
        $response =   $this->EventsService->getEventRecipients($id);
        return $response;
    }
    
}
