<?php

namespace App\Http\Controllers;

use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolSubjectController extends Controller
{
    public function createSubject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|max:255',
            'subject_code' => 'nullable|string|max:255',
            'subject_description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subject = SchoolSubject::create($request->all());

        return response()->json([
            'message' => 'Subject created successfully',
            'subject' => $subject
        ], 201);
    }

    public function getAllSubjects()
    {
        $subjects = SchoolSubject::all();
        return response()->json(['subjects' => $subjects]);
    }
}
