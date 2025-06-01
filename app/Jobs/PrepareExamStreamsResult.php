<?php

namespace App\Jobs;

use App\Models\SchoolExamSchoolClassSchoolClassStream;
use App\Models\SchoolExamSchoolClassSubject;
use App\Models\StudentSchoolClassStream;
use App\Models\StudentSchoolExamSchoolClassSchoolClassStream;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use DB;
use Log;

class PrepareExamStreamsResult implements ShouldQueue
{
    use Queueable;

    public $streams;
    public $exam;
    public $school_exam_school_class_id;

    /**
     * Create a new job instance.
     *
     * @param array $streams
     * @param array $exam
     * @param mixed $school_exam_school_class_id
     */
    public function __construct($streams, $exam, $school_exam_school_class_id)
    {
        $this->streams = $streams;
        $this->exam = $exam;
        $this->school_exam_school_class_id = $school_exam_school_class_id;
    }


    public function handle(): void
    {
        try {
            DB::beginTransaction();

            Log::info('Job started for streams:', ['streams' => $this->streams]);

            foreach ($this->streams as $stream) {
                $getAllStreamsStudentsIds = StudentSchoolClassStream::where('school_class_stream_id', $stream)
                    ->where("status", "ACTIVE")
                    ->pluck('student_id')
                    ->toArray();

                $schoolExamSchoolClassSchoolClassStreamsId = SchoolExamSchoolClassSchoolClassStream::
                    where('school_exam_school_class_id', $this->school_exam_school_class_id)
                    ->where('school_class_stream_id', $stream)
                    ->pluck('id');

                Log::info('Processing stream', [
                    'stream_id' => $stream,
                    'students' => $getAllStreamsStudentsIds,
                    'stream_class_ids' => $schoolExamSchoolClassSchoolClassStreamsId
                ]);

                $examClassSubjects = SchoolExamSchoolClassSubject::where('school_exam_school_class_id', $this->school_exam_school_class_id)->get();

                foreach ($getAllStreamsStudentsIds as $studentId) {
                    foreach ($examClassSubjects as $examClassSubject) {
                        StudentSchoolExamSchoolClassSchoolClassStream::updateOrCreate([
                            'school_exam_school_class_subject_id' => $examClassSubject->id,
                            'school_exam_school_class_school_class_streams_id' => $schoolExamSchoolClassSchoolClassStreamsId->first(),
                            'student_id' => $studentId
                        ], [
                            'score' => 0.00,
                            'percentage_score' => 0.00,
                            'grade_id' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
            Log::info('Job completed successfully for streams:', ['streams' => $this->streams]);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Job failed with error: ' . $exception->getMessage(), [
                'exception' => $exception,
                'streams' => $this->streams,
                'school_exam_school_class_id' => $this->school_exam_school_class_id
            ]);
            throw $exception; 
        }
    }


    protected function calculateResult($stream)
    {
        return rand(50, 100);  // Dummy result calculation
    }
}

