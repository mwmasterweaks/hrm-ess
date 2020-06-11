<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RALog extends Model
{
    protected $fillable = [
        'employeeID', 'datetime'
    ];
}
