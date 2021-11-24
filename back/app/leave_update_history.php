<?php

namespace App;

use App\User;
use App\Employee;
use Illuminate\Database\Eloquent\Model;

class leave_update_history extends Model
{
    protected $fillable = [
        'old_details', 'updated_details', 'user_id', 'employee_id', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->hasOne(Employee::class, 'id', 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
