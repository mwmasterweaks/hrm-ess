<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class leave_day extends Model
{
    protected $fillable = [
        'leave_id', 'leave_date', 'halfday', 'halfday_type', 'created_at', 'updated_at'
    ];
}
