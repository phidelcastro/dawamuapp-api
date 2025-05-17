<?php
namespace App\Http\Services;

use App\Http\Requests\CreateExamRequest;
use App\Jobs\PrepareExamStreamsResult;
use App\Models\SchoolClassExam;
use App\Models\SchoolClassSchoolSubject;
use App\Models\SchoolClassStream;
use App\Models\SchoolExam;
use App\Models\SchoolExamSchoolClass;
use App\Models\SchoolExamSchoolClassSchoolClassStream;
use App\Models\SchoolExamSchoolClassSubject;
use App\Models\Student;
use App\Models\StudentSchoolClassStream;
use App\Models\StudentSchoolExamSchoolClassSchoolClassStream;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;


class ExamService
{
    public function createExam(Request $request)
    {
        DB::beginTransaction();
        $uploadedFiles = [];
        $savedData = [
            'exam' => null,
            'classes' => [],
        ];

        try {
            // Parse exam info
            $examInfo = json_decode($request->input('examinfo'), true);
            $classesConfigs = json_decode($request->input('classesConfigs'), true);

            if (!$examInfo || !is_array($examInfo)) {
                return response()->json(['error' => 'Invalid or missing exam info.'], 422);
            }

            if (!$classesConfigs || !is_array($classesConfigs)) {
                return response()->json(['error' => 'Missing or invalid classesConfigs.'], 422);
            }

            // Create exam
            $exam = SchoolExam::create([
                'exam_label' => $examInfo['examName'],
                'Year' => $examInfo['startDate'],
                'start_date' => $examInfo['startDate'],
                'end_date' => $examInfo['endDate'],
                'exam_status' => 'Active',
                'note' => $examInfo['notes'] ?? null,
                'target' => 'school',
                'exam_type' => 'END OF TERM',
                'school_term' => $examInfo['term'],
            ]);

            $savedData['exam'] = $exam;

            foreach ($classesConfigs as $i => $classConfig) {
                if (!isset($classConfig['subjects']) || !isset($classConfig['streams'])) {
                    return response()->json(['error' => "Invalid class config at index $i."], 422);
                }

                // Create class entry
                $classEntry = SchoolExamSchoolClass::create([
                    'school_exam_id' => $exam->id,
                    'school_class_id' => $classConfig['id'],
                ]);

                $savedClass = [
                    'class' => $classEntry,
                    'subjects' => [],
                    'streams' => []
                ];

                foreach ($classConfig['subjects'] as $j => $subject) {
                    if (empty($subject['subjectDetails'])) {
                        return response()->json(['error' => "Missing subjectDetails at class $i, subject $j"], 422);
                    }

                    $subjectRecord = SchoolExamSchoolClassSubject::updateOrCreate(
                        [
                            'school_exam_school_class_id' => $classEntry->id,
                            'school_class_school_subject_id' => $subject['subjectDetails']['id'],
                        ],
                        [
                            'total_score' => $subject['score'] ?? 0,
                            'status' => 'Active',
                        ]
                    );

                    $fileKey = "classesConfigs_files.$i.$j";
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        $path = $file->store('exam_files', 'public');
                        $subjectRecord->update(['exam_paper_link' => $path]);
                        $uploadedFiles[] = $path;
                    }

                    $savedClass['subjects'][] = $subjectRecord;
                }

                foreach ($classConfig['streams'] as $k => $stream) {
                    if (empty($stream['streamDetails'])) {
                        return response()->json(['error' => "Missing streamDetails at class $i, stream $k"], 422);
                    }

                    $streamEntry = SchoolExamSchoolClassSchoolClassStream::create([
                        'school_exam_school_class_id' => $classEntry->id,
                        'school_class_stream_id' => $stream['streamDetails']['id'],
                        'status' => 'Active',
                    ]);

                    $savedClass['streams'][] = $streamEntry;
                }
                //create students results via jobs and queues.   
                $allclassstreams = SchoolClassStream::where("school_class_id", $classConfig['id'])->pluck("id");
                PrepareExamStreamsResult::dispatch($allclassstreams->toArray(), $exam, $classEntry->id);
                //create student results via jobs and queues.

                $savedData['classes'][] = $savedClass;
            }


            DB::commit();
            return response()->json([
                'message' => 'Exam created successfully.',
                'data' => $savedData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedFiles as $filePath) {
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }

            return response()->json(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }



    public function createExamOld(Request $request)
    {
        try {
            DB::beginTransaction();

            return response()->json($request->classesConfigs);

            // $created_exam = SchoolExam::create([
            //     'exam_label' => $request->exam_label,
            //     'Year' => $request->Year,
            //     'start_date' => $request->start_date,
            //     'end_date' => $request->end_date,
            //     'exam_status' => 'Active',
            //     'note' => $request->note,
            //     'target' => $request->target,
            //     'exam_type' => $request->exam_type,
            //     'school_term' => $request->school_term,
            // ]);
            // Attach classes
            foreach ($request->targetClasses as $targetClass) {
                $schoolExamSchoolClass = SchoolExamSchoolClass::create([
                    'school_exam_id' => $created_exam->id,
                    'school_class_id' => $targetClass,
                ]);

                // Attach streams for the class
                $allclassstreams = SchoolClassStream::where("school_class_id", $targetClass)
                    ->whereIn("id", $request->targetStreams)
                    ->pluck('id');

                foreach ($allclassstreams as $targetStream) {
                    SchoolExamSchoolClassSchoolClassStream::create([
                        'school_exam_school_class_id' => $schoolExamSchoolClass->id,
                        'school_class_stream_id' => $targetStream,
                        'status' => 'Active',
                    ]);

                }

            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Exam created.',
                'data' => $created_exam,
            ]);

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Exam has not been created.',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function addSubjectsToExams($request)
    {
        try {
            DB::beginTransaction();

            $exampfound = SchoolExam::where("id", $request->exam)->first();
            if ($exampfound) {
                foreach ($request->subjects as $subject) {
                    // You will need to get the school_exam_school_class_id
                    // Probably you are saving somewhere the relation between exam and class
                    $school_exam_school_class_id = getSchoolExamSchoolClassId($request->exam, $subject['class']);
                    // ^^^ You need a function like this based on your database design

                    foreach ($subject['classsubjects'] as $classsubject) {
                        if (SchoolClassSchoolSubject::where("school_class_id", $classsubject)->exists()) {
                            SchoolExamSchoolClassSubject::updateOrCreate(
                                [
                                    'school_exam_school_class_id' => $school_exam_school_class_id,
                                    'school_class_school_subject_id' => $classsubject['school_class_school_subject_id'],
                                ],
                                [
                                    'exam_paper_link' => $classsubject['exam_paper_link'],
                                    'total_score' => $classsubject['total_score'],
                                    'status' => $classsubject['status'],
                                ]
                            );
                        }


                    }
                }
                //create students results via jobs and queues.   
                $allclassstreams = SchoolClassStream::where("school_class_id", $subject['class'])->pluck("id");
                PrepareExamStreamsResult::dispatch($allclassstreams->toArray(), $exampfound, $school_exam_school_class_id);
                //create student results via jobs and queues.
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Exam subjects added successfully.',

            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Exam subjects were not added successfully.',
                'error' => $exception->getMessage()

            ]);
        }
    }
    public function getAllExams()
    {
        $exams = SchoolExam::with([
            'SchoolExamSchoolClass.SchoolClassDetails',
            'SchoolExamSchoolClass.subjects.subjectDetails',
            'SchoolExamSchoolClass.streams'
        ])->get();
        return response()->json(['exams' => $exams]);

    }
    public function getExamsByClass($classId, $request)
    {
        $exams = SchoolExam::whereHas('SchoolExamSchoolClass', function ($query) use ($classId) {
            $query->where('school_class_id', $classId);
        })
            ->paginate($request->perPage ? $request->perPage : 10);
        return response()->json(['exams' => $exams]);

    }
    public function getExamEligibleStudentsByClass($classId, $examId)
    {
        $students = StudentSchoolClassStream::
            join("school_class_streams", "school_class_streams.id", "=", "student_school_class_streams.school_class_stream_id")
            ->join("school_classes", "school_classes.id", "=", "school_class_streams.school_class_id")
            ->join("school_exam_school_classes", "school_exam_school_classes.school_class_id", "=", "school_classes.id")
            ->join("students", "students.id", "=", "student_school_class_streams.student_id")
            ->join("users", "users.id", "=", "students.user_id")
            ->where("school_classes.id", $classId)
            ->where("school_exam_school_classes.school_exam_id", $examId)
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
            ->paginate(10);
        return response()->json(['students' => $students]);
    }

    public function getStudentResultsByExam($classId, $examId)
    {
        $results = StudentSchoolExamSchoolClassSchoolClassStream::
            join(
                "school_exam_school_class_subjects",
                "school_exam_school_class_subjects.id",
                "=",
                "student_school_exam_school_class_school_class_streams.school_exam_school_class_subject_id"
            )
            ->join(
                "school_exam_school_classes",
                "school_exam_school_classes.id",
                "=",
                "school_exam_school_class_subjects.school_class_school_subject_id"
            )
            ->join(
                "school_exam_school_class_school_class_streams",
                "school_exam_school_class_school_class_streams.id",
                "=",
                "student_school_exam_school_class_school_class_streams.school_exam_school_class_school_class_streams_id"
            )
            ->join(
                "school_class_streams",
                "school_class_streams.id",
                "school_exam_school_class_school_class_streams.school_class_stream_id"
            )
            ->join(
                "school_class_school_subjects",
                "school_class_school_subjects.id",
                "school_exam_school_class_subjects.school_class_school_subject_id"
            )
            ->join(
                "school_classes",
                "school_classes.id",
                "school_class_streams.school_class_id"
            )
            ->join(
                "school_subjects",
                "school_subjects.id",
                "school_class_school_subjects.school_subject_id"
            )
            ->join(
                "students",
                "students.id",
                "student_school_exam_school_class_school_class_streams.student_id"
            )
            ->join(
                "users",
                "users.id",
                "students.user_id"
            )
            // ->where("school_exam_school_classes.school_exam_id",$examId)
            ->where("school_classes.id", $classId)
            ->select(
                "student_school_exam_school_class_school_class_streams.id as result_id",
                "school_exam_school_class_subjects.*",
                "students.*",
                "school_classes.id as class_id",
                "school_class_streams.id as stream_id",
                "school_classes.class_name",
                "school_class_streams.stream_name",
                "users.*",
                "school_exam_school_class_school_class_streams.school_class_stream_id",
                "student_school_exam_school_class_school_class_streams.*"
            )
            ->get();
        return response()->json(['success' => true, 'results' => $results]);
    }
    public function getStudentExamResults($request)
    {

        $results = Student::with([
            'user',
            'studentSchoolExamSchoolClassSchoolClassStream' => function ($query) use ($request) {
                $query->whereHas('schoolExamSchoolClassSchoolClassStream.schoolClassStream.schoolClass', function ($q) use ($request) {
                    if ($request->class) {
                        Log::info('Filtering loaded relation by school_class_id = ' . $request->class);
                        $q->where('school_classes.id', $request->class);
                    }
                    if ($request->stream) {
                        $q->where('school_class_streams.id', $request->stream);
                    }

                });
            }
        ])
            ->whereHas('studentSchoolExamSchoolClassSchoolClassStream.schoolExamSchoolClassSchoolClassStream.schoolClassStream.schoolClass', function ($query) use ($request) {
                //   return $request->class;
                Log::info('Entered whereHas for relation' . $request->class);
                if ($request->class) {
                    Log::info('Entered whereHas for relation qw' . $request->class);
                    $query->where('school_classes.id', $request->class);
                }
                if ($request->stream) {
                    $query->where('school_class_streams.id', $request->stream);
                }

            })
            ->whereHas('studentSchoolExamSchoolClassSchoolClassStream.schoolExamSchoolClassSubject', function ($query2) use ($request) {
                $query2->whereHas('schoolExamSchoolClass', function ($q) use ($request) {

                });
            })
            ->whereHas("StudentSchoolClassStream.SchoolClassStream", function ($query3) use ($request) {

                if ($request->class) {
                    Log::info('' . $request->class);
                    $query3->where('school_class_id', $request->class);
                }
            })->paginate($request->perPage ?? 10);
        return response()->json(['success' => true, 'results' => $results]);
    }
    public function getExamSubjects(Request $request)
    {
        $subjects = SchoolExamSchoolClassSubject::with([
            'schoolClassSchoolSubject.subjectDetails'
        ])->whereHas("schoolClassSchoolSubject.subjectDetails", function ($query1) use ($request) {

        })->whereHas("SchoolExamSchoolClass.SchoolClassDetails", function ($query1) use ($request) {
            if ($request->filled("class")) {
                $query1->where("school_class_id", $request->class);
            }
            if ($request->filled("exam")) {
                $query1->where("school_exam_id", $request->exam);
            }
        })
            ->select(['school_exam_school_class_subjects.*'])
            ->paginate($request->perPage ? $request->$request : 10);
        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    public function getExamClasses(Request $request)
    {
        $classes = SchoolExamSchoolClass::with(['SchoolClassDetails'])
            ->whereHas("SchoolClassDetails", function ($query1) use ($request) {
                if ($request->filled("exam")) {
                    $query1->where("school_exam_id", $request->exam);
                }
                if ($request->filled("class")) {
                    $query1->where("school_class_id", $request->class);
                }
            })
            ->paginate($request->perPage ? $request->$request : 10);
        return response()->json(['success' => true, 'results' => $classes]);
    }
    public function getExamStreams(Request $request)
    {
        $classes = SchoolExamSchoolClassSchoolClassStream::with(['SchoolClassStream.schoolClass'])->whereHas("schoolClassStream", function ($query1) use ($request) {
            if ($request->filled("class")) {
                $query1->where("school_class_id", $request->class);
            }
        })
        ->whereHas("examClass", function ($query2) use ($request) {

                if ($request->filled("exam")) {
                    $query2->where("school_exam_id", $request->exam);
                }

        })
        ->paginate($request->perPage ? $request->$request : 10);
        return response()->json(['success' => true, 'streams' => $classes]);

    }
    public function updateStudentExamResult(Request $request){
        $results=$request->results;
        foreach($results as $key=>$value){
          StudentSchoolExamSchoolClassSchoolClassStream::where("id",$key)->update(['score'=>$value]);
        }
            return response()->json([
                'message' => 'Exam results recorded successfullsuccessfully.',
                'data' => $results
            ]);
    }

}
