<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClassSchoolSubject extends Model
{    protected $fillable=['school_class_id','school_subject_id'];
    public function SchoolExamSchoolClassSubject(){
       return $this->hasMany(SchoolExamSchoolClassSubject::class);
    }
}
