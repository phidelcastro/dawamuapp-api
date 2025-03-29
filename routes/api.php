<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolSubjectController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\SchoolClassStreamController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register-student', [AuthController::class, 'registerStudent']);
    Route::post('/register-staff', [AuthController::class, 'registerStaff']);
    Route::post('/register-parent', [AuthController::class, 'registerParent']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/health', [AuthController::class, 'health']);
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Restrict route to users with 'admin' role
    Route::middleware(['role:super admin'])->get('/admin', function () {
        return response()->json(['message' => 'Welcome, Admin']);
    });
    Route::middleware(['role:super admin'])->prefix('admin')->group(function () {
        Route::post('/create-class', [SchoolClassController::class, 'createClass']);
        Route::post('/create-subject', [SchoolSubjectController::class, 'createSubject']);
        Route::post('/create-stream', [SchoolClassStreamController::class, 'createStream']);

         });

    Route::prefix('utilities')->group(function () {
        Route::get('/get-classes', [UtilityController::class, 'getAllClasses']);
        Route::get('/get-subjects', [UtilityController::class, 'getAllSubjects']);
        Route::get('/get-streams', [UtilityController::class, 'getAllStreams']);
        Route::get('/get-streams-by-class/{classId}', [UtilityController::class, 'getStreamsByClass']);
        });  
 



});
