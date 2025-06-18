<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolExamSchoolClass extends Model
{
    protected $fillable = [
        'school_exam_id',
        'school_class_id',
        'status'
    ];
    
    public function SchoolExam(){
        return $this->belongsTo(SchoolExam::class,"school_exam_id");
    }
    public function SchoolClassDetails(){
        return $this->belongsTo(SchoolClass::class,"school_class_id");
    }
    public function subjects(){
        return $this->hasMany(SchoolExamSchoolClassSubject::class);
    }
    public function streams(){
        return $this->hasMany(SchoolExamSchoolClassSchoolClassStream::class);
    }
    
    
}
