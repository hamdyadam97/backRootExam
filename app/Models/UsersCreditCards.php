<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersCreditCards extends Model
{
    protected $table = 'users_credit_cards';
//    public $timestamps=false;


    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


}
