<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\SchoolClass;
use App\Models\SchoolClassStream;

class UtilityController extends Controller
{
    //

    public function getAllSubjects()
    {
        $subjects = SchoolSubject::all();
        return response()->json(['subjects' => $subjects]);
    }

    public function getAllClasses(Request $request)
    {
        $classes = SchoolClass::paginate($request->perPage?$request->perPage:2);
        return response()->json(['classes' => $classes]);
    }

    public function getAllStreams()
    {
        $streams = SchoolClassStream::with('schoolClass')->get();
        return response()->json(['streams' => $streams]);
    }

    public function getStreamsByClass($classId)
    {
        $streams = SchoolClassStream::where('school_class_id', $classId)->get();
        return response()->json(['streams' => $streams]);
    }

 
}
