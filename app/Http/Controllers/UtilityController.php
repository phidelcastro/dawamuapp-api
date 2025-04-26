<?php

namespace App\Http\Controllers;

use App\Http\Services\ExamService;
use App\Models\SchoolExam;
use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\SchoolClass;
use App\Models\SchoolClassStream;

class UtilityController extends Controller
{
    protected $examservice;
    public function __construct(ExamService $examService){
          $this->examservice=$examService;
    }
    
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
    public function getAllExams(){
        $exams= $this->examservice->getAllExams();
        return response()->json(['exams' => $exams],201);

    }
    public function getExamsByClass($classId){
        $exams= $this->examservice->getExamsByClass($classId);
        return response()->json(['exams' => $exams],201);
    }

 
}
