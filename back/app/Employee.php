<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Group;
use App\Rate;
use App\employee_position;
use App\branch;
use App\department;
use App\deduction;
use App\earning;
use App\dtr;
use App\payslip;
use App\Role;
use App\over_time;
use App\employee_status_history;
use App\Approver;
use App\Employee_approver;

class Employee extends Model
{
    protected $fillable = [
        'id', 'group_id', 'rate_id', 'position_id', 'branch_id', 'department_id', 'employment_status',
        'first_name', 'middle_name', 'last_name', 'date_hired', 'img', 'gender',
        'permanent_address', 'tel_no', 'mobile_no', 'email1', 'email2', 'provindial_address',
        'provincial_tel_no', 'birth_date', 'birth_place', 'nationality', 'religion', 'sss_no',
        'pag-ibig', 'prc_license', 'civil_status', 'height', 'weight', 'tin_no', 'philhealth_no',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'id');
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }

    public function rate()
    {
        return $this->hasOne(Rate::class, 'id', 'rate_id');
    }

    public function position()
    {
        return $this->hasOne(employee_position::class, 'id', 'position_id');
    }

    public function branch()
    {
        return $this->hasOne(branch::class, 'id', 'branch_id');
    }

    public function department()
    {
        return $this->hasOne(department::class, 'id', 'department_id');
    }

    public function dtr()
    {
        return $this->hasMany(dtr::class, 'employee_id', 'id');
        //->where('pay_period_id', $this->payPeriod);
    }

    public function employeeDTR($payPeriod)
    {
        return $this->videos();
    }

    public function deduction()
    {
        return $this->hasMany(deduction::class, 'employee_id', 'id')->orderBy("created_at", "DESC");
    }

    public function earning()
    {
        return $this->hasMany(earning::class, 'employee_id', 'id')->orderBy("created_at", "DESC");
    }

    public function payslip()
    {
        return $this->hasMany(payslip::class, 'employee_id', 'id')->orderBy("created_at", "DESC");
    }

    public function overtime()
    {
        return $this->hasMany(over_time::class, 'employee_id', 'id');
    }

    public function employeeStatus()
    {
        return $this->hasMany(employee_status_history::class, 'employee_id', 'id');
    }

    public function approver()
    {
        return $this->hasOne(Approver::class, 'employee_id', 'id');
    }

    public function emp_approver()
    {
        return $this->hasMany(Employee_approver::class, 'employee_id', 'id')->orderBy('level');
    }
    // public function emp_roles()
    // {
    //     return $this->hasManyThrough(

    //         'App\Role',
    //         'App\User',
    //         'employee_id', // Foreign key on users table...
    //         'user_id', // Foreign key on roles table...
    //         'id', // Local key on roles table...
    //         'id' // Local key on users table...
    //     );
    // }
}
