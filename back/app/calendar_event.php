<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\branch;

class calendar_event extends Model
{
    protected $fillable = [
        'name', 'description', 'type', 'frequency', 'branch_id', 'from', 'to', 'created_at', 'updated_at'
    ];

    public function branch()
    {
        return $this->hasOne(branch::class, 'id', 'branch_id');
    }
}
