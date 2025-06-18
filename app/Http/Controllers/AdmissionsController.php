<?php

namespace App\Http\Controllers;

use App\Http\Services\AdmissionService;
use Illuminate\Http\Request;

class AdmissionsController extends Controller
{
    protected $admissionsservice;
    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;

    }
    public function newAdmission(Request $request)
    {
        $response = $this->admissionService->newAdmission($request);
        return $response;
    }
    public function getParents()
    {


    }
}
