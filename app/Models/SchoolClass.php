<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    //

    protected $fillable = [
        'class_name',
        'class_code',
        'class_description',
        'school_id', // Uncomment if you include school_id
    ];
   
    public function examDetails(){
        return $this->hasMany(SchoolExamSchoolClass::class);
    }
}
