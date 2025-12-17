<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Questions;

class Questionanswers extends Model
{
    use HasFactory;

    protected $table = 'questions_answers';

    protected $fillable = [
        'exam_id','question_id','answer_option','answer','created_at','updated_at'
    ];

    public function questions()
    {
        return $this->belongsTo(Questions::class);
    }
}
