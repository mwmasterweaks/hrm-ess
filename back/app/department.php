<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class department extends Model
{
    protected $fillable = [
        'name', 'description', 'created_at', 'updated_at'
    ];
}
