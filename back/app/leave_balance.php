<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\leave_type;

class leave_balance extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type_id', 'enroll_year', 'balance', 'availed', 'accrued', 'created_at', 'updated_at'
    ];

    public function leave_type()
    {
        return $this->hasOne(leave_type::class, 'id', 'leave_type_id');
    }
}
