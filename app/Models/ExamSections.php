<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSections extends Model
{
    use HasFactory;

    protected $table = 'exam_sections';

    public $fillable = ['question_id' , 'section_id'];
}
