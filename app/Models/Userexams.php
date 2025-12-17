<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Userexams extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_exams';

    protected $fillable = [
        'user_id','exam_id','score','user_exams_date','created_at','updated_at','deleted_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Model\Exams::class);
    }
    public static $status = [
        0 => 'New',
        1 => 'In progress',
        2 => 'Submitted',
    ];
}
