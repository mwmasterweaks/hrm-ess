<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\leave;
use App\missing_time_log;
use App\over_time;
use App\change_shift;
use App\change_rest_day;
use App\manual_attendance;


//For multiple attachment (for missing time logs and leave application) - Wilma
class attachment extends Model
{
    protected $fillable = [
        'reference_no','filename'

    ];

    public function Leave()
    {
        return $this->hasMany(leave::class,'reference_no','reference_no');
    }

    public function missing_time_log()
    {
        return $this->hasMany(missing_time_log::class,'reference_no','reference_no');
    }

    public function over_time()
    {
        return $this->hasMany(over_time::class,'reference_no','reference_no');
    }

    public function change_shift()
    {
        return $this->hasMany(change_shift::class,'reference_no', 'reference_no');
    }

    public function change_rest_day()
    {
        return $this->hasMany(change_rest_day::class,'reference_no','reference_no');
    }

    public function manual_attendance()
    {

        return $this->manual_attendance(manual_attendance::class,'reference_no','reference_no');
    }
}
