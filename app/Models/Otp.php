<?php

namespace App\Models;   

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'guard_name', 
        'guard_email',
        'guard_status', 
        'otp',
        'expires_at'
    ];


}
