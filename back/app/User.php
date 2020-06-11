<?php

namespace App;

use App\Role;
use App\Employee;
use App\Approver;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    protected $fillable = [
        'employee_id', 'email', 'password', 'created_at', 'updated_at'
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function approver()
    {
        return $this->hasOne(Approver::class, 'employee_id', 'employee_id');
    }
}
