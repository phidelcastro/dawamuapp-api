<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentSchoolParentMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_parent_message_id',
        'parent_id',
        'delivered_via_email',
        'email_delivery_time',
        'delivered_via_phone',
        'phone_delivery_time',
        'delivered_via_push',
        'push_delivery_time',
        'parent_comment',
        'response_logs',
    ];

    protected $casts = [
        'response_logs' => 'array',
        'email_delivery_time' => 'datetime',
        'phone_delivery_time' => 'datetime',
        'push_delivery_time' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(SchoolParentMessage::class, 'school_parent_message_id');
    }

    public function parent()
    {
        return $this->belongsTo(Guardian::class, 'parent_id')->with('user');
    }
}
