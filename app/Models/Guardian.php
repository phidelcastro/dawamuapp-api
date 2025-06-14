<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    
    protected $fillable = [
    'relationship',
    'town',
    'city',
    'address',
    'box_number',
    'zip_code',
    'user_id',
]; public function user()
    {
        return $this->belongsTo(User::class);
    }
public function students()
{
    return $this->hasMany(Student::class);
}


}
