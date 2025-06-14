<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_employment',
        'staff_id',
        'professional_registration_number',
        'level_of_education',
        'years_of_experience_prior_employment',
        'status',
    ];
    
}
