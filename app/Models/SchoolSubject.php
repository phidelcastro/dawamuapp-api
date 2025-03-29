<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSubject extends Model
{
    protected $fillable = [
        'subject_name',
        'subject_code',
        'subject_description'
    ];
}
