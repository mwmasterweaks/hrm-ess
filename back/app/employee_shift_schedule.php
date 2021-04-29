<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class employee_shift_schedule extends Model
{
    protected $fillable = [
        'employee_id', 'pay_period_id', 'shift_schedule_id'
    ];
}
