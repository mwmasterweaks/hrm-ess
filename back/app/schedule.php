<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
    protected $fillable = [
        'work_date', 'day', 'sched_in', 'sched_out', 'is_rest_day'
    ];
}
