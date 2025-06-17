<?php

namespace App\Http\Controllers;

use App\Http\Services\ClassManagementService;
use App\Http\Services\ExamService;
use App\Http\Services\FileUploadService;
use App\Http\Services\TeacherService;
use App\Http\Services\UtilityService;
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
    protected $utilityservice;
    public function __construct(ExamService $examService, ClassManagementService $classManagementService, FileUploadService $fileUploadService, TeacherService $teacherService,UtilityService $utilityservice)
    {
        $this->examservice = $examService;
        $this->classmanagementservice = $classManagementService;
        $this->fileUploadService=$fileUploadService;
        $this->teachersservice=$teacherService;
        $this->utilityservice=$utilityservice;
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
        $query = SchoolClassStream::query();
        if ($request->has('name')) {
            $query->where('stream_name', 'like', '%' . $request->name . '%');
        }
    
        if ($request->has('class')) {
            $query->where('school_class_id', $request->class);
        }
        $perPage = $request->get('perPage', 2); // Default to 2
        $streams = $query->paginate($perPage);
    
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
    public function getAllRoles(Request $request){
         $teachers = $this->utilityservice->getAllRoles($request);
        return $teachers; 
    }

        public function getParents(Request $request){
         $teachers = $this->utilityservice->getParents($request);
        return $teachers; 
    }
         public function getStaff(Request $request){
         $teachers = $this->utilityservice->getStaff($request);
        return $teachers; 
    }

    
  
}
