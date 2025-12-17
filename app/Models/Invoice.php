<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'user_package_id',
        'invoice_number',
        'total_amount',
        'status',
        'sent_to_accounting',
        'sent_at'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function userPackage()
    {
        return $this->belongsTo(Userpackges::class, 'user_package_id');
    }
}
