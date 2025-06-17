<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMedicalHistoryImage extends Model
{
    //
    protected $fillable=[
        'student_medical_history_id',
        'path'
    ];
    public function studentMedicalHistory(){
        return $this->belongsTo(StudentMedicalHistory::class);
    }
}
