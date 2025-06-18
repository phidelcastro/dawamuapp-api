<?php
namespace App\Http\Services;

use App\Models\SchoolClassSchoolSubject;
use App\Models\SchoolClassStreamTeacherSubject;
use App\Models\SchoolTerm;
use App\Models\Student;
use App\Models\StudentSchoolClassStream;
use App\Models\User;
use App\Models\Guardian;
use App\Models\Teacher;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class ClassManagementService
{
    public function addSubjectsToClass($request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->subjects as $subject) {
                SchoolClassSchoolSubject::updateOrCreate([
                    'school_class_id' => $subject['class_id'],
                    'school_subject_id' => $subject['subject_id']
                ]);
            }
            DB::commit();
            return response()->json(['sucess' => true, 'message' => 'Subject added successfully']);
        } catch (Exception $exception) {

            DB::rollBack();
            return response()->json(['sucess' => false, 'message' => 'Subject were not added.']);
        }
    }
    public function addStudentToStream($request)
    {
        try {
            DB::beginTransaction();
            StudentSchoolClassStream::updateOrCreate(
                [
                    "student_id" => $request->student,
                    "school_class_stream_id" => $request->stream
                ],
                [
                    "start_date" => $request->start_date,
                    "end_date" => $request->end_date,
                    "status" => 'Active'
                ]
            );

            DB::commit();
            return response()->json(['sucess' => true, 'message' => 'Student added successfully']);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['sucess' => false, 'message' => 'Student was not added.']);
        }

    }
    public function getClassStudents()
    {
        $students = StudentSchoolClassStream::
            join("school_class_streams", "school_class_streams.id", "=", "student_school_class_streams.school_class_stream_id")
            ->join("school_classes", "school_classes.id", "=", "school_class_streams.school_class_id")
            ->join("students", "students.id", "=", "student_school_class_streams.student_id")
            ->join("users", "users.id", "=", "students.user_id")
            ->select(
                'students.id AS student_id',
                'students.student_admission_number',
                'students.user_id',
                'students.admitted_on_school_class_id',
                'students.date_of_admission',
                'students.status AS student_account_status',
                'first_name',
                'middle_name',
                'last_name',
                'date_of_birth',
                'account_status AS user_account_status',
                'gender',
                'phone_number',
                'closed_at',
                'email'
            )
            ->get();
        return response()->json(['sucess' => false, 'students' => $students]);
    }
    public function getSubjectsByClass($classId, Request $request)
    {
        $class_subjects = SchoolClassSchoolSubject::
            join("school_subjects", "school_subjects.id", "=", "school_class_school_subjects.school_subject_id")
            ->join("school_classes", "school_classes.id", "=", "school_class_school_subjects.school_class_id")
            ->where("school_class_id", $classId)
            ->select("school_class_school_subjects.id", "school_classes.class_name", "school_subjects.subject_name", "school_class_school_subjects.created_at as created_at")
            ->paginate($request->perPage ? $request->perPage : 10);
        return response()->json(['subjects' => $class_subjects]);
    }
    public function getStudents($request)
    {
        $students = Student::join("users", "users.id", "=", "students.user_id")
            ->join("student_school_class_streams", "student_school_class_streams.student_id", "=", "students.id")
            ->join("school_class_streams", "school_class_streams.id", "=", "student_school_class_streams.school_class_stream_id")
            ->join("school_classes", "school_classes.id", "=", "school_class_streams.school_class_id");

        if ($request->filled("class")) {
            $students->where("school_classes.id", $request->class);
        }
        if ($request->filled("stream")) {
            $students->where("school_class_streams.id", $request->stream);
        }
        if ($request->filled("student_status")) {
            $students->where("student_school_class_streams.status", $request->student_status);
        }

        if ($request->filled("search")) {
            $students->where(function ($query) use ($request) {
                $search = $request->input('search');
                $query->where("first_name", "LIKE", "%{$search}%")
                    ->orWhere("last_name", "LIKE", "%{$search}%");
            });
        }
        $students = $students->select(
            "students.*",
            DB::raw("CONCAT(first_name, ' ',middle_name,' ', last_name) AS full_name"),
            "school_class_streams.stream_name",
            "school_classes.class_name",
            "student_school_class_streams.start_date as stream_admission_date",
            "student_school_class_streams.school_class_stream_id",
            "student_school_class_streams.end_date as stream_exit_date",
            "students.date_of_admission as date_admitted_to_school",
            "users.first_name",
            "users.middle_name",
            "users.last_name",
            "users.email",
            "users.phone_number",
            "users.gender",
            "users.date_of_birth"
        )

            ->paginate($request->perPage ? $request->perPage : 10);
        return response()->json(['sucess' => false, 'students' => $students]);
    }
    public function registerStudentAndAssignStream($request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string',
                'middle_name' => 'required|string',
                'last_name' => 'required|string',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:Male,Female,Other',
                'phone_number' => 'nullable|string',
                'email' => 'required|email|unique:users',
                'date_of_admission' => 'required|date'
            ]);

            $request->password = generateRandomPassword();

            DB::beginTransaction();

            $counter = DB::table('admission_counters')
                ->where('key', 'student')
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                DB::table('admission_counters')->insert([
                    'key' => 'student',
                    'value' => 1,
                ]);
                $next = 1;
            } else {
                $next = $counter->value + 1;

                DB::table('admission_counters')
                    ->where('key', 'student')
                    ->update(['value' => $next]);
            }


            DB::table('admission_counters')
                ->where('key', 'student')
                ->update(['value' => $next]);

            $admissionNumber = 'STD' . str_pad($next, 9, '0', STR_PAD_LEFT);


            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'account_status' => 'ACTIVE'
            ]);
            $role = Role::findByName('student');
            $user->assignRole($role);
            $student = Student::create([
                'user_id' => $user->id,
                'student_admission_number' => $admissionNumber,
                'date_of_admission' => $request->date_of_admission,
                'status' => 'ACTIVE'
            ]);
            $student_stream = StudentSchoolClassStream::updateOrCreate(
                [
                    "student_id" => $student->id,
                    "school_class_stream_id" => $request->stream_id
                ],
                [
                    "start_date" => $request->start_date ?? date("Y-m-d"),
                    "end_date" => $request->end_date,
                    "status" => 'Active'
                ]
            );
            DB::commit();
            return response()->json([
                'message' => 'Student registered and linked successfully',
                'data' => [
                    'user' => $user,
                    'student' => $student,
                    'student_stream' => $student_stream
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateStudentAndStream($request, $studentId)
    {
        try {
            $request->validate([
                'first_name' => 'required|string',
                'middle_name' => 'required|string',
                'last_name' => 'required|string',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:Male,Female,Other',
                'phone_number' => 'nullable|string',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'date_of_admission' => 'required|date',
                'school_class_stream_id' => 'required|integer',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);

            DB::beginTransaction();

            $student = Student::findOrFail($studentId);
            $user = $student->user;

            // Update user details
            $user->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
            ]);

            // Update student info (without changing admission number)
            $student->update([
                'date_of_admission' => $request->date_of_admission,
                'status' => 'ACTIVE'
            ]);

            // Update or create stream assignment
            $student_stream = StudentSchoolClassStream::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'school_class_stream_id' => $request->school_class_stream_id,
                ],
                [
                    'start_date' => $request->start_date ?? date('Y-m-d'),
                    'end_date' => $request->end_date,
                    'status' => 'Active'
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Student details updated successfully',
                'data' => [
                    'user' => $user,
                    'student' => $student,
                    'student_stream' => $student_stream
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSchoolTerms(Request $request)
    {
        $terms = SchoolTerm::all();
        return response()->json(['sucess' => false, 'terms' => $terms]);
    }

    public function getDashboard(Request $request)
    {
        if ($request->filled("class")) {
            $students = StudentSchoolClassStream::
                join("school_class_streams", "school_class_streams.id", "=", "student_school_class_streams.school_class_stream_id")
                ->where("school_class_streams.school_class_id", $request->class)
                ->count();
            $gurdian = StudentSchoolClassStream::
                join("school_class_streams", "school_class_streams.id", "=", "student_school_class_streams.school_class_stream_id")
                ->join("students", "students.id", "=", "student_school_class_streams.student_id")
                ->groupBy("students.guardian_id")
                ->where("school_class_streams.school_class_id", $request->class)
                ->count();
            $universityIntake = 0;
            $teachers = SchoolClassStreamTeacherSubject::join("school_class_streams", "school_class_streams.id", "=", "school_class_stream_teacher_subjects.school_class_stream_id")
                ->join("teacher_subjects", "teacher_subjects.id", "=", "school_class_stream_teacher_subjects.teacher_subject_id")
                ->where("school_class_streams.school_class_id", $request->class)
                ->groupBy("teacher_subjects.teacher_id")
                ->count();
            $subjects = SchoolClassSchoolSubject::where("school_class_id", $request->class)->count();
            return response()->json([
                'sucess' => false,
                'students' => $students,
                'gurdian' => $gurdian,
                'universityIntake' => $universityIntake,
                'teachers' => $teachers,
                'subjects' => $subjects
            ]);
        }
        $students = Student::count();
        $gurdian = Guardian::count();
        $teachers = Teacher::count();
        $fees = 0;
        $universityIntake = 20;
        return response()->json(['sucess' => false, 'students' => $students, 'gurdian' => $gurdian, 'universityIntake' => $universityIntake, 'teachers' => $teachers]);
    }


}