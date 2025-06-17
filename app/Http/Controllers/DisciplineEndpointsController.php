<?php

namespace App\Http\Controllers;

use App\Http\Services\MobileEndpointsUtilityService;
use App\Http\Services\DisciplineEndpointsService;
use Auth;
use Illuminate\Http\Request;
use Validator;


class DisciplineEndpointsController extends Controller
{
 
    protected $disciplineEndpointsService;
    public function __construct(DisciplineEndpointsService $disciplineEndpointsService){
        $this->disciplineEndpointsService= $disciplineEndpointsService;
    }
 
 public function saveIndisciplineCase(Request $request){
 $input = array_merge($request->all(), ['reported_by' => Auth::id()]);
$validator = Validator::make($input, [
    'student_id' => 'required|string|exists:students,id',
    'location' => 'required|string|max:255',
    'offense' => 'required|string',
    'action_taken' => 'required|string',
    'parent_notification' => 'boolean',
    'follow_up' => 'required|string',
    'notes' => 'nullable|string',
    'images.*' => 'nullable|image',
    'reported_by' => 'required|exists:users,id',
]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $report = $this->disciplineEndpointsService->saveIndisciplineCase($request);

        return response()->json([
            'message' => 'Discipline report submitted successfully',
            'data' => $report,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to submit report'.Auth::id(),
            'error' => $e->getMessage(),
        ], 500);
    }
   }
}
