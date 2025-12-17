<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionTopic extends Model
{
    use HasFactory;

    protected $table = 'question_topics';

    public $fillable = ['question_id' , 'topic_id'];

}
