<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamQuestionsAnswer extends Model
{
    use HasFactory;
    protected $table = 'user_exam_questions_answers';

    protected $fillable = [
        'id','user_id','exam_id','question_id','answer','created_at','updated_at',
    ];
}
