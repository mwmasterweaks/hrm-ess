<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\deduction_type;

class deduction extends Model
{
    protected $fillable = ['employee_id', 'deduction_type_id', 'effective_date', 'end_date', 'amount'];

    public function type()
    {
        return $this->hasOne(deduction_type::class, 'id', 'deduction_type_id');
    }
}
