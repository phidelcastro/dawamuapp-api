<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_admission_number',
        'date_of_admission',
        'status',
        'guardian_id',
        'admitted_on_school_class_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function studentSchoolExamSchoolClassSchoolClassStream(){
        return $this->hasMany(StudentSchoolExamSchoolClassSchoolClassStream::class);
    }
    public function StudentSchoolClassStream(){
        return $this->hasMany(StudentSchoolClassStream::class);
    }
    public function guardian()
{
    return $this->belongsTo(Guardian::class);
}

public function otherContacts()
{
    return $this->hasMany(OtherContact::class);
}

    
}
