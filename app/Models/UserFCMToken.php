<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFCMToken extends Model
{
    //
    protected $fillable = [
        'user_id',
        'token',
        'phone_type',
        'status',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
