<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamTrialDetails extends Model
{

    protected $fillable = [
        'exam_trial_id', 'question_id', 'answer_id', 'is_correct', 'is_marked'
    ];

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id');
    }

}
