<?php

namespace App\Http\Controllers;

use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use App\Http\Services\FileUploadService;
use App\Models\SchoolExam;
use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\SchoolClass;
use App\Models\SchoolClassStream;

class UtilityController extends Controller
{
    protected $examservice;
    protected $classmanagementservice;
    protected $fileUploadService;
    public function __construct(ExamService $examService, ClassManagementService $classManagementService, FileUploadService $fileUploadService)
    {
        $this->examservice = $examService;
        $this->classmanagementservice = $classManagementService;
        $this->fileUploadService=$fileUploadService;
    }

    public function getAllSubjects()
    {
        $subjects = SchoolSubject::all();
        return response()->json(['subjects' => $subjects]);
    }
    public function getSubjectsByClass($classId, Request $request)
    {
        $response = $this->classmanagementservice->getSubjectsByClass($classId, $request);
        return $response;
    }

    public function getAllClasses(Request $request)
    {
        $classes = SchoolClass::paginate($request->perPage ? $request->perPage : 2);
        return response()->json(['classes' => $classes]);
    }

    public function getAllStreams(Request $request)
    {
        $streams = SchoolClassStream::paginate($request->perPage ? $request->perPage : 2);
        return response()->json(['streams' => $streams]);
    }

    public function getStreamsByClass($classId, Request $request)
    {
        $streams = SchoolClassStream::where('school_class_id', $classId)
            ->paginate($request->perPage ? $request->perPage : 2);

        return response()->json(['streams' => $streams]);
    }
    public function getAllExams()
    {
        $exams = $this->examservice->getAllExams();
        return response()->json(['exams' => $exams], 201);

    }
    public function getExamsByClass($classId, Request $request)
    {
        $exams = $this->examservice->getExamsByClass($classId, $request);
        return $exams;
    }
    public function getStudentsByClass($classId)
    {
        $exams = $this->classmanagementservice->getClassStudents($classId);
        return response()->json(['exams' => $exams], 201);
    }
    public function getExamEligibleStudentsByClass($classId, $examId)
    {

        $exams = $this->examservice->getExamEligibleStudentsByClass($classId, $examId);
        return $exams;

    }
    public function recordExamSubjectResults()
    {


    }
    public function getExamSubjectsByClassAndExam($classId, $examId)
    {
        $exams = $this->examservice->getExamSubjectsByClassAndExam($classId, $examId);
        return response()->json(['exams' => $exams], 201);
    }
    public function getStudentResultsByExam($classId,$examId)
    {
        $exams = $this->examservice->getStudentResultsByExam($classId,$examId);
        return $exams;
    }
    public function getStudentExamResults(Request $request)
    {
        $exams = $this->examservice->getStudentExamResults($request);
        return $exams;
    }
    public function uploadExamPaperTemporarily(Request $request){
        $exams =$this->fileUploadService->uploadExamPaperTemporarily($request);
        return $exams;
    }
    public function getExamSubjects(Request $request){
        $subjects =$this->examservice->getExamSubjects($request);
        return $subjects;
    }
    public function getExamClasses(Request $request){
          $classes =$this->examservice->getExamClasses($request);
        return $classes;
    }
    public function getExamStreams(Request $request){
           $classes =$this->examservice->getExamStreams($request);
        return $classes;
    }

}
