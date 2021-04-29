<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'name', 'daily_rate', 'sss_deduction', 'phic_deduction', 'hdmf_deduction'
    ];
}
