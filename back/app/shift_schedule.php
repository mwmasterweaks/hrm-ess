<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class shift_schedule extends Model
{
    protected $fillable = [
        'name', 'pay_period_id', 'schedule_id'
    ];
}
