<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class manual_attendance extends Model
{
    protected $fillable = [
        'employee_id', 'reference_no', 'work_date', 'shift', 'time_in', 'time_out',
        'reason', 'attachment', 'date_filed', 'status', 'approve_level', 'created_at', 'updated_at'
    ];
}
