<?php
namespace App\Http\Services;
use App\Models\Guardian;
use App\Models\SchoolStaff;
use App\Models\StudentDiscipline;
use App\Models\StudentMedicalHistory;
use App\Models\UserFCMToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use DB;
class MobileEndpointsUtilityService
{
    public function saveUserFCMToken(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'token' => 'required|string',
            'phone_type' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

          $deviceToken = UserFCMToken::updateOrCreate(
    [ 'user_id' => $validated['userId'] ],
    [
        'token' => $validated['token'],
        'phone_type' => $validated['phone_type'] ?? null
     ]
);


            DB::commit();

            return response()->json([
                'message' => 'Device token saved successfully',
                'data' => $deviceToken,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to save device token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStudentDiscipline(Request $request)
    {

        $medical = StudentDiscipline::where("student_id", $request->student)->get()->toArray();
        return response()->json(['data' => $medical, 'parent' => $medical]);
    }
    public function getStudentMedical(Request $request)
    {

        $medical = StudentMedicalHistory::where("student_id", $request->student)->get()->toArray();
        return response()->json(['data' => $medical, 'parent' => $medical]);

    }
    public function getStudentResults(Request $request, $jsonres = true)
    {
        $examRecords = DB::table('student_school_exam_school_class_school_class_streams')
            ->join('school_exam_school_class_subjects', 'school_exam_school_class_subjects.id', '=', 'student_school_exam_school_class_school_class_streams.school_exam_school_class_subject_id')
            ->join('school_class_school_subjects', 'school_class_school_subjects.id', '=', 'school_exam_school_class_subjects.school_class_school_subject_id')
            ->join('school_subjects', 'school_subjects.id', '=', 'school_class_school_subjects.school_subject_id')
            ->join('school_exam_school_classes', 'school_exam_school_classes.id', '=', 'school_exam_school_class_subjects.school_exam_school_class_id')
            ->join('school_classes', 'school_classes.id', '=', 'school_exam_school_classes.school_class_id')
            ->join('grading_systems', 'grading_systems.id', "=", 'student_school_exam_school_class_school_class_streams.grade_id')
            ->where('student_id', $request->student)
            ->where('school_exam_id', $request->exam)
            ->where('school_exam_school_classes.school_class_id', $request->class)
            ->select(
                'student_school_exam_school_class_school_class_streams.*',
                'school_exam_school_classes.school_exam_id',
                'school_subjects.subject_name',
                'grading_systems.grade',
                'grading_systems.comment'
            )
            ->get();
        $totalScore = $examRecords->sum('score');
        $subjectNames = $examRecords->pluck('subject_name')->unique()->values();
        if ($jsonres) {
            return response()->json([
                'exam_subject_records' => $examRecords,
                'total_score' => $totalScore,
                'subjectNames' => $subjectNames
            ]);
        } else {
            return [
                'exam_subject_records' => $examRecords,
                'total_score' => $totalScore,
                'subjectNames' => $subjectNames
            ];
        }

    }
}