<?php

namespace App;

use App\Employee;
use Illuminate\Database\Eloquent\Model;

class employee_status_history extends Model
{
    protected $fillable = [
        'employee_id', 'status', 'start_date', 'end_date', 'created_at', 'updated_at'
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
