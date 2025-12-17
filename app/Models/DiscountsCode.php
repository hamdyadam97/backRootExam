<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountsCode extends Model
{

    protected $guarded = [];

    public function hyperPays()
    {
        return $this->hasMany(HyperpayResults::class, 'coupon')->where('is_success');
    }

    public function moneyLogs()
    {
        return $this->hasMany(MoneyLogs::class, 'coupon' , 'code');
    }
}
