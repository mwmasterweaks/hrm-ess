<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class biometric_attendance extends Model
{
    protected $fillable = [
        'employee_id', 'punch_time', 'type', 'latitude', 'longitude', 'approve_level',
        'status', 'created_at', 'updated_at'
    ];
}
