<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_admission_number',
        'date_of_admission',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
