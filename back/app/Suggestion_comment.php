<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\employees;


class Suggestion_comment extends Model
{
    protected $fillable = [

        'suggestion_id', 'user_id', 'comment', 'status'


    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(employees::class, 'id', 'employee_id');
    }
}
