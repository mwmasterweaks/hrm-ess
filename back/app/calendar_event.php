<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class calendar_event extends Model
{
    protected $fillable = [
        'name', 'description', 'type', 'frequency', 'from', 'to', 'created_at', 'updated_at'
    ];
}
