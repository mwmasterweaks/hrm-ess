<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Group;

class Pay_period extends Model
{
    protected $fillable = [
        'period', 'frequency', 'group_id', 'year', 'from', 'to', 'created_at', 'updated_at'
    ];

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
