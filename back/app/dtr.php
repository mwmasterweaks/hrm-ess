<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class dtr extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'employee_id', 'pay_period_id', 'work_date', 'day', 'shift_sched_in', 'shift_sched_out',
        'time_in', 'time_out', 'is_rest_day', 'created_at', 'updated_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id', 'employee_id');
    }
}
