<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDiscipline extends Model
{
    protected $table = 'student_disciplines';

    protected $fillable = [
        'student_id',
        'location',
        'offense',
        'action_taken',
        'parent_notification',
        'follow_up',
        'reported_by',
        'notes',
        'images',
        'status',
        'status'
    ];

    protected $casts = [
        'parent_notification' => 'boolean',
        'images' => 'array',
    ];
   protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
    /**
     * The student who the report is about.
     */
    public function student(): BelongsTo
    {
        // Assuming Student model primary key matches student_id type
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    /**
     * The user who reported this discipline.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by', 'id');
    }
}
