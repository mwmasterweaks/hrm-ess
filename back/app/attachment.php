<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\leave;
use App\missing_time_log;
use App\over_time;

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
}
