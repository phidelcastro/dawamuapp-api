<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClassSchoolSubject extends Model
{

    protected $fillable = ['school_class_id', 'school_subject_id'];
    public function subjectDetails()
    {
        return $this->belongsTo(SchoolSubject::class, "school_subject_id");
    }
    public function classDetails()
    {
        return $this->belongsTo(SchoolClass::class, "school_class_id");
    }

}
