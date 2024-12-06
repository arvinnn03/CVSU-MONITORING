<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';

    protected $fillable = [
                'visitor_name', 
                'visitor_email', 
                'visitor_mobile_no',  
                'visitor_meet_person_name', 
                'visitor_department', 
                'visitor_image',
                'visitor_reason_to_meet', 
                'visitor_enter_time', 
                // 'visitor_outing_remark', 
                'visitor_out_time', 
                'visitor_status', 
                'visitor_enter_by',
                'visitor_enter_out_by', // Add this line
                'unique_token'
    ];

    // In Visitor.php (Model)
    public function user()
    {
        return $this->belongsTo(User::class, 'visitor_enter_by');
    }

}
