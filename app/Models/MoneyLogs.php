<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyLogs extends Model
{
    protected $table = 'money_logs';

    protected $fillable = [
        'platform',
        'item_id',
        'unique_id',
        'payment_id',
        'status',
        'user_id',
        'coupon'
    ];
//    public function getOrder()
//    {
//        return $this->belongsTo('App\Models\Orders', 'item_id', 'id');
//    }


}
