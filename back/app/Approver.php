<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Employee;

class Approver extends Model
{
    protected $fillable = [
        'employee_id', 'created_at', 'updated_at'
    ];


    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
