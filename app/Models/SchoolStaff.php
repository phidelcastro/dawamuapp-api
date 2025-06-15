<?php

namespace App\Models;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class SchoolStaff extends Model {
        use HasFactory;

    protected $table = 'school_staff';

    protected $fillable = [
        'user_id',
        'date_of_employment',
        'staff_id',
        'professional_registration_number',
        'level_of_education',
        'years_of_experience_prior_employment',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
