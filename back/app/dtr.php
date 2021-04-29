<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class dtr extends Model
{
    protected $fillable = [
        'employee_id', 'work_date', 'day', 'shift_sched_in', 'shift_sched_out', 'is_rest_day'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id', 'employee_id');
    }
}
