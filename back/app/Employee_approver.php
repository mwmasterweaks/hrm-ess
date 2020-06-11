<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee_approver extends Model
{
    protected $fillable = [
        'approver_id', 'employee_id', 'level'
    ];
}
