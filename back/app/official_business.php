<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class official_business extends Model
{
    protected $fillable = [
        'employee_id', 'reference_no', 'work_date', 'shift', 'time_in', 'time_out', 'with_break',
        'total_hours', 'reason', 'attachment', 'date_filed', 'status', 'approve_level', 'created_at', 'updated_at'
    ];
}
