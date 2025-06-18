<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExam extends Model
{
       protected $fillable = [
        'exam_label',
        'Year',
        'start_date',
        'end_date',
        'exam_status',
        'note',
        'target',
        'exam_type',
        'school_term',
    ];
    public function SchoolExamSchoolClass(){
        return $this->hasMany(SchoolExamSchoolClass::class);
    }
       protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
