<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
//use App\employees;


class Suggestion extends Model
{
    protected $fillable =[
         'title', 'message', 'user_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    //public function employee()
    //{
    //    return $this->hasOne(employees::class, 'id', 'user_id');
   // }

    
    
}
