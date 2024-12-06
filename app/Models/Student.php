<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'stud_id',
        'student_department',
        'student_course',
        'student_enter_time',
        'student_out_time',
        'student_status',
        'unique_token'
    ];

    protected $casts = [
        'student_enter_time' => 'datetime',
        'student_out_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            $student->unique_token = Str::random(32);
        });
    }
}
