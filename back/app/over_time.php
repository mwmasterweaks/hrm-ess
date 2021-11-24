<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class over_time extends Model
{
    protected $fillable = [
        'employee_id', 'reference_no', 'type', 'work_date', 'shift', 'time_in', 'time_out', 'with_break', 'break_hours', 'total_hours', 'reason', 'attachment', 'date_filed', 'status', 'approve_level', 'created_at', 'updated_at'
    ];
}
