<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolParentMessage extends Model
{
        use HasFactory;

    protected $fillable = ['types', 'subject', 'content', 'created_by'];

    protected $casts = [
        'types' => 'array',
    ];

    public function parentDeliveries()
    {
        return $this->hasMany(ParentSchoolParentMessage::class);
    }
       protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
