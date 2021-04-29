<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\earning_type;

class earning extends Model
{
    protected $fillable = ['employee_id', 'earning_type_id', 'effective_date', 'end_date', 'amount'];

    public function type()
    {
        return $this->hasOne(earning_type::class, 'id', 'earning_type_id');
    }
}
