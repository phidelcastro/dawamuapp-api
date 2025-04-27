<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSchoolClassStream extends Model
{
    //
    
    protected $fillable=["id", "student_id", "school_class_stream_id", "start_date", "end_date", "status"];
}
