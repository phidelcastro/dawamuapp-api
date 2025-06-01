<?php
namespace App\Http\Services;

use App\Models\SchoolClassStreamTeacherSubject;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Validator;

class TeacherService
{
    public function registerTeacherByAdmin(Request $request)
    {
        $request->password = generateRandomPassword();
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'phone_number' => 'required|string|unique:users,phone_number',
            'email' => 'required|email|unique:users,email',
            'staff_id' => 'nullable|string',
            'tsc_number' => 'nullable|string',
            'level_of_education' => 'required|string',
            'years_of_experience_prior_employment' => 'required|string',
            'date_of_employment' => 'required|date',
            'subjects' => 'required|array|min:2',
            'subjects.*.id' => 'required|integer|distinct',
            'subjects.*.is_preferred' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subjects = $request->input('subjects', []);
        $hasPreferred = collect($subjects)->contains('is_preferred', true);
        if (!$hasPreferred) {
            return response()->json([
                'status' => 'error',
                'message' => 'At least one subject must be marked as preferred.',
            ], 422);
        }

        // Check duplicates for staff_id and tsc_number only if they are present and not blank
        if ($request->filled('staff_id')) {
            $existsStaff = Teacher::where('staff_id', $request->staff_id)->exists();
            if ($existsStaff) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The staff ID has already been taken.',
                ], 422);
            }
        }

        if ($request->filled('tsc_number')) {
            $existsTsc = Teacher::where('tsc_number', $request->tsc_number)->exists();
            if ($existsTsc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The TSC number has already been taken.',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'account_status' => 'ACTIVE',
            ]);
            $role = Role::findByName('teacher');
            $user->assignRole($role);


            $teacher = Teacher::create([
                'user_id' => $user->id,
                'staff_id' => $request->staff_id,
                'level_of_education' => $request->level_of_education,
                'tsc_number' => $request->tsc_number,
                'years_of_experience_prior_employment' => $request->years_of_experience_prior_employment,
                'date_of_employment' => $request->date_of_employment,
            ]);

            foreach ($subjects as $subject) {
                $exists = TeacherSubject::where('teacher_id', $teacher->id)
                    ->where('school_subject_id', $subject['id'])
                    ->exists();
                if (!$exists) {
                    TeacherSubject::updateOrCreate(
                        [
                            'teacher_id' => $teacher->id,
                            'school_subject_id' => $subject['id']
                        ],
                        [
                            'is_main' => $subject['is_preferred'] ? 'Yes' : 'No'
                        ]
                    );
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Teacher registered successfully.',
                'data' => $teacher->load('teacherSubjects'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register teacher. Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getTeachers(Request $request)
    {
        $teachers = Teacher::with([
            'teacherSubjects.schoolSubject',
            'teacherSubjects.teacherStreamSubjects' // this loads streams for each subject
        ])
            ->join("users", "users.id", "=", "teachers.user_id")
            ->select(
                DB::raw("CONCAT(users.first_name, ' ', users.middle_name, ' ', users.last_name) AS full_name"),
                'teachers.id',
                'teachers.id as teacher_id',
                'teachers.staff_id',
                'teachers.level_of_education',
                'teachers.tsc_number',
                'teachers.years_of_experience_prior_employment',
                'teachers.date_of_employment',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.date_of_birth',
                'users.gender',
                'users.phone_number',
                'users.email',
                'users.account_status'
            )
            ->orderBy("teachers.id", "DESC")
            ->paginate($request->perPage ?? 10);

        return response()->json([
            'status' => 'success',
            'message' => 'Teachers fetched successfully.',
            'teachers' => $teachers
        ]);
    }
    public function detachTeacherStreamSubjects(Request $request)
    {
        DB::beginTransaction();

        try {
            $deleted = SchoolClassStreamTeacherSubject::where('teacher_subject_id', $request->teacher_subject_id)
                ->where('school_class_stream_id', $request->school_class_stream_id)
                ->delete();
            if (!$deleted) {
                throw new \Exception('Record not found or already deleted.');
            }

            DB::commit();
            return response()->json(['message' => 'Record deleted successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function registerTeacherStreamSubjects(Request $request)
    {
        $data = $request->all();
        DB::beginTransaction();
        try {
            foreach ($data as $entry) {
                $teacherId = $entry['teacher_id'];
                $streamId = $entry['stream_id'];
                $subjectIds = $entry['subject_ids'];
                foreach ($subjectIds as $subjectId) {
                    $teacherSubject = TeacherSubject::where('teacher_id', $teacherId)
                        ->where('school_subject_id', $subjectId)
                        ->first();
                    if (!$teacherSubject) {
                        throw new \Exception("TeacherSubject not found for teacher_id $teacherId and subject_id $subjectId");
                    }
                    SchoolClassStreamTeacherSubject::updateOrCreate(
                        [
                            'teacher_subject_id' => $teacherSubject->id,
                            'school_class_stream_id' => $streamId
                        ],
                        [
                            'is_stream_class_teacher' => 'No',
                            'start_date' => now(),
                            'end_date' => now()->addMonths(3),
                            'current_status' => 'Active',
                        ]
                    );
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Subjects assigned successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to assign subjects: ' . $e->getMessage(),
            ], 500);
        }
    }

}
