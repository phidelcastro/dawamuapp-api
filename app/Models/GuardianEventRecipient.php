<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuardianEventRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_event_id',
        'guardian_id',
        'phone_delivery_status',
        'email_delivery_status',
        'push_status',
        'status',
        'responded_at',
        'comment',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(GuardianEvent::class, 'guardian_event_id');
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}
