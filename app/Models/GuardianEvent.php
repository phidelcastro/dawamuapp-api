<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardianEvent extends Model
{
    use HasFactory;

    protected $table = 'guardian_events';

    protected $fillable = [
        'name',
        'date',
        'confirm_by',
        'requirements',
        'location',
        'notification_types',
        'reminder_frequency',
        'reminder_days_before',
        'remind_until',
        'parents',
        'banner_path',
    ];

    protected $casts = [
        'notification_types' => 'array',
        'parents' => 'array',
        'date' => 'date',
        'confirm_by' => 'date',
        'remind_until' => 'date',
    ];

    public function recipients()
    {
        return $this->hasMany(GuardianEventRecipient::class);
    }
}
