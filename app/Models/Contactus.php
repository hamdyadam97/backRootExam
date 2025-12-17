<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contactus extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'contact_us';

    protected $fillable = [
        'user_id','title','description','created_at','updated_at','deleted_at',
    ];

}
