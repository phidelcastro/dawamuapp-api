<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherContact extends Model
{
     use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'relationship',
        'phone',
        'email',
        'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
