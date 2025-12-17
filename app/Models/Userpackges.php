<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Userpackges extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_packages';

    protected $fillable = [
        'user_id','package_id','start_date','end_date','created_at','updated_at','deleted_at',
    ];

    public function getPackage()
    {
        return $this->belongsTo(Packges::class,'package_id');
    }
    public function getHyperPay()
    {
        return $this->belongsTo(HyperpayResults::class,'pay_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function invoice()
{
    return $this->hasOne(\App\Models\Invoice::class, 'user_package_id');
}

}
