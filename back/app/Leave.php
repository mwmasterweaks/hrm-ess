<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\leave_type;
use App\Employee_approver;
use App\Employee;
use App\leave_day;
use App\attachment;

class Leave extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type_id', 'reference_no', 'date_from', 'date_to', 'total_days',
        'reason', 'attachment', 'date_filed', 'status', 'approve_level', 'created_at', 'updated_at'
    ];

    public function leave_type()
    {
        return $this->hasOne(leave_type::class, 'id', 'leave_type_id');
    }

    public function employee_approver()
    {
        return $this->hasOne(Employee_approver::class, 'employee_id', 'employee_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function leave_days()
    {
        return $this->hasMany(leave_day::class, 'leave_id', 'id');
    }


        //For multiple attachment - Wilma
    public function attachment(){

        return $this->hasMany(attachment::class,'reference_no', 'reference_no');
    }
}
