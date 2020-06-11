<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'name', 'daily_rate', 'regular_ot_rate', 'holiday_ot_rate', 'regular_holiday_rate', 'special_holiday_rate', 'night_differencial', 'undertime_rate', 'late_rate', 'created_at', 'updated_at'
    ];
}
