<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClassStream extends Model
{
    protected $fillable = [
        'school_class_id',
        'stream_name',
        'stream_code',
        'stream_description'
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
