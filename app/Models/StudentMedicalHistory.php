<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentMedicalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'complaint',
        'procedure',
        'medicines',
        'next_checkup_date',
        'alert_parent',
        'refer_external',
    ];

    protected $casts = [
        'date' => 'date',
        'next_checkup_date' => 'date',
        'alert_parent' => 'boolean',
        'refer_external' => 'boolean',
    ];
   protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
    protected $appends = ['student_full_name', 'alert_parent_text', 'refer_external_text'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getStudentFullNameAttribute()
    {
        return $this->student?->full_name ?? '';
    }

    public function getAlertParentTextAttribute()
    {
        return $this->alert_parent ? 'Yes' : 'No';
    }

    public function getReferExternalTextAttribute()
    {
        return $this->refer_external ? 'Yes' : 'No';
    }
    public function studentMedicalHistoryImages(){
        return $this->hasMany(StudentMedicalHistoryImage::class);
    }
}
