<?php

use App\Http\Controllers\AdmissionsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolSubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\SchoolClassStreamController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register-student', [AuthController::class, 'registerStudent']);
    Route::post('/register-staff', [AuthController::class, 'registerStaff']);
    Route::post('/register-parent', [AuthController::class, 'registerParent']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/assign-user-admin', [AuthController::class, 'assignUserAdmin']);
});

Route::get('/health', [AuthController::class, 'health']);
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::middleware(['role:super admin'])->get('/admin', function () {
        return response()->json(['message' => 'Welcome, Admin']);
    });

    Route::middleware(['role:super admin'])->prefix('admin')->group(function () {
        Route::post('/create-class', [SchoolClassController::class, 'createClass']);
        Route::post('/create-subject', [SchoolSubjectController::class, 'createSubject']);
        Route::post('add-subjects-to-class', [SchoolClassController::class, 'addSubjectsToClass']);
        Route::post('/create-stream', [SchoolClassStreamController::class, 'createStream']);
        Route::post('/add-class-subject', [SchoolClassController::class, 'addClassSubject']);
        Route::post('/add-class-exam', [SchoolClassController::class, 'addClassExam']);
        Route::post('create-exam', [SchoolClassController::class, 'createExam']);
        Route::post('add-subjects-to-exam', [SchoolClassController::class, 'addSubjectsToExams']);
        Route::post('add-student-to-stream', [SchoolClassController::class, 'addStudentToStream']);
        Route::post('update-student-exam-result', [SchoolClassController::class, 'updateStudentExamResult']);
        Route::post('register-student-and-assign-stream', [SchoolClassController::class, 'registerStudentAndAssignStream']);
        Route::put('/update-student/{student}', [SchoolClassController::class, 'updateStudentAndStream']);
        Route::post('/register-teacher', [SchoolClassController::class, 'registerTeacher']);
        Route::post('/register-teacher-stream-subjects', [SchoolClassController::class, 'registerTeacherStreamSubjects']); 
        Route::post('/detach-teacher-subject-from-stream', [SchoolClassController::class, 'detachTeacherStreamSubjects']); 
         Route::post('/register-new-admission', [AdmissionsController::class, 'newAdmission']); 
        
        
    });

    Route::prefix('utilities')->group(function () {
        Route::get('/get-classes', [UtilityController::class, 'getAllClasses']);
        Route::get('/get-roles', [UtilityController::class, 'getAllRoles']);
        Route::get('/get-subjects', [UtilityController::class, 'getAllSubjects']);
        Route::get('/get-streams', [UtilityController::class, 'getAllStreams']);
        Route::get('/get-streams-by-class', [UtilityController::class, 'getStreamsByClass']);
        Route::get('/get-exams', [UtilityController::class, 'getAllExams']);
        Route::get("get-exams-by-class/{classId}", [UtilityController::class, 'getExamsByClass']);
        Route::get("get-student-by-class/{classId}", [UtilityController::class, 'getStudentsByClass']);
        Route::get("get-students", [UtilityController::class, 'getStudents']);
        Route::get("get-subjects-by-class/{classId}", [UtilityController::class, 'getSubjectsByClass']);
        Route::get("get-exam-eligible-students-by-class/{classId}/{examId}", [UtilityController::class, 'getExamEligibleStudentsByClass']);
        Route::get("get-student-results-by-class-and-exam/{classId}/{examId}", [UtilityController::class, "getStudentResultsByExam"]);
        Route::get("get-student-exam-subjects", [UtilityController::class, "getStudentExamResults"]);
        Route::post("upload-exam-paper-temporary", [UtilityController::class, "uploadExamPaperTemporarily"]);
        Route::get("get-exam-subjects", [UtilityController::class, "getExamSubjects"]);
        Route::get("get-exam-classes", [UtilityController::class, "getExamClasses"]);
        Route::get("get-exam-streams", [UtilityController::class, "getExamStreams"]);
        Route::get("get-single-student-exam-results", [UtilityController::class, "getSingleStudentResults"]);
        Route::get("get-school-terms", [UtilityController::class, "getSchoolTerms"]);
        Route::get("get-teachers", [UtilityController::class, "getTeachers"]);
    });

    Route::prefix('student')->group(function () {
        Route::get("get-student-latest-exam", [StudentController::class, "getLatestStudentExam"]);
        Route::get("get-student-prev-exam", [StudentController::class, "getPreviousExamForAStudent"]);
        Route::get("get-student-exam-summary", [StudentController::class, "getLatestExamDetails"]);
    });

});
