<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class change_rest_day extends Model
{
    protected $fillable = [
        'employee_id', 'reference_no', 'work_date', 'shift', 'type', 'time_in', 'time_out', 'reason', 'attachment', 'date_filed', 'status', 'approve_level', 'created_at', 'updated_at'
    ];
}
