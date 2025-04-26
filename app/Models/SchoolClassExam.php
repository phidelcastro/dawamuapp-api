<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClassExam extends Model
{
    //
    protected $fillable = [
        'school_exam_id',
        'school_class_id',
        'status'
    ];
}
