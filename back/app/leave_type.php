<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class leave_type extends Model
{
    protected $fillable = [
        'name', 'created_at', 'updated_at'
    ];
}
