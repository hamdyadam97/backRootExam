<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exams extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'exams';

    protected $fillable = [
        'cat_id','sub_cat_id','title','description','icon','time','type','status','score','created_at','updated_at','deleted_at',
    ];

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];
    public function userExams()
    {
        return $this->hasMany(Userexams::class);
    }
    public static $exam_type = [
        1 => " نماذج اختبار",
        2 => "نماذج حلول",
    ];
    public function questions()
    {
        return $this->belongsToMany(Questions::class,'exam_questions','question_id','exam_id');
    }


    public static function getExamMode()
    {
        return [
            0 => 'Tatur Mode',
            1 => 'Exam Mode',
        ];
    }

}
