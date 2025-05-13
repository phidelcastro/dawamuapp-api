<?php

namespace App\Jobs;

use App\Models\SchoolExamSchoolClassSchoolClassStream;
use App\Models\SchoolExamSchoolClassSubject;
use App\Models\StudentSchoolClassStream;
use App\Models\StudentSchoolExamSchoolClassSchoolClassStream;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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

        // try
        // {
        //  DB::beginTransaction();
        //  DB::commit();
        // }catch(Exception $expetion){
        //  DB::rollBack();

        // }
        dump('Job is being processed'); 
        // \Log::info('Processing job for stream:'.$this->streams);
        foreach ($this->streams as $stream) {       
            $getAllStreamsStudentsIds = StudentSchoolClassStream::where('school_class_stream_id', $stream)
            ->pluck('student_id')
            ->toArray();
            $schoolExamSchoolClassSchoolClassStreamsId = SchoolExamSchoolClassSchoolClassStream::
                 where('school_exam_school_class_id', $this->school_exam_school_class_id)
                ->where('school_class_stream_id', $stream)
                ->pluck('id');
                // \Log::info('getAllStreamsStudentsIds:'.$getAllStreamsStudentsIds);
                dump($stream);
                dump($getAllStreamsStudentsIds); 
                \Log::info('schoolExamSchoolClassSchoolClassStreamsId:'.$schoolExamSchoolClassSchoolClassStreamsId);
            $examClassSubjects = SchoolExamSchoolClassSubject::where('school_exam_school_class_id', $this->school_exam_school_class_id)->get();
            foreach ($getAllStreamsStudentsIds as $getAllStreamsStudentsId) {   
                dump("we are on loop 2");
                dump("examClassSubjects",$examClassSubjects);
                foreach ($examClassSubjects as $examClassSubject) {
                   
                    StudentSchoolExamSchoolClassSchoolClassStream::updateOrCreate([
                        'school_exam_school_class_subject_id' => $examClassSubject->id, 
                        'school_exam_school_class_school_class_streams_id' => $schoolExamSchoolClassSchoolClassStreamsId->first(),
                        'student_id' => $getAllStreamsStudentsId],[
                        'score'=>0.00,
                        'percentage_score'=>0.00,
                        'grade_id'=>1,
                    ]);
                }
            }
        }
        // dump('Job processed successfully'.$this->streams);

    }

    protected function calculateResult($stream)
    {
        return rand(50, 100);  // Dummy result calculation
    }
}

