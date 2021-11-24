<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Approver;

class Employee_approver extends Model
{
    protected $fillable = [
        'approver_id', 'employee_id', 'level'
    ];

      public function approver()
    {
        return $this->hasOne(Approver::class, 'id', 'approver_id');
    }
}
