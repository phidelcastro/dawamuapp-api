<?php

namespace App\Http\Controllers;

use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use App\Http\Services\FileUploadService;
use App\Http\Services\TeacherService;
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
    protected $teachersservice;
    public function __construct(ExamService $examService, ClassManagementService $classManagementService, FileUploadService $fileUploadService, TeacherService $teacherService)
    {
        $this->examservice = $examService;
        $this->classmanagementservice = $classManagementService;
        $this->fileUploadService=$fileUploadService;
        $this->teachersservice=$teacherService;
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

    public function getStreamsByClass( Request $request)
    {
        $streams = SchoolClassStream::query();
        if($request->filled("class")){

             $streams->where('school_class_id', $request->class);
        }
       $streams= $streams->select('*')->paginate($request->perPage ? $request->perPage : 2);

        return response()->json(['streams' => $streams]);
    }
    public function getAllExams(Request $request)
    {
        $exams = $this->examservice->getAllExams($request);
        return  $exams;

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
      public function getSingleStudentResults(Request $request){
          $response =  $this->examservice->getSingleStudentResults($request);
        return response()->json([$response]);
    }
    public function getStudents(Request $request)
    {
        $students = $this->classmanagementservice->getStudents($request);
        return $students;
    }
    public function getSchoolTerms(Request $request){
        $terms = $this->classmanagementservice->getSchoolTerms($request);
        return $terms;
    }
    public function getTeachers(Request $request){
        $teachers = $this->teachersservice->getTeachers($request);
        return $teachers;
    }
  
}
