<?php

namespace App\Models;

use App\Models\Questionanswers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questions extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questions';

    const FILLABLE = [
        'text_question', 'notes', 'status', 'question_type', 'answer_type',
        'hint', 'show_answer_explanation', 'show_hint', 'show_answer', 'show_video',
        'video_link', 'time_minutes', 'answer_has_image', 'question_has_image',
        'correct_answer_id', 'category_id', 'sub_category_id', 'sub_subcategory_id',
        'question_image', 'answer_image',

    ];
    protected $fillable = self::FILLABLE;

    public static $status = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public static $questionType = [
        0 => '',
        1 => 'question',
        2 => 'text',
        // 3 => 'video',
        // 4 => 'other',
    ];

    public static $answer_type = [
        1 => 'Radio',
        2 => 'Multiple choice',
    ];

    public function questions_answers()
    {
        return $this->hasMany(Questionanswers::class, 'question_id', 'id');
    }

    public function exam_sections()
    {
        return $this->hasMany(ExamSections::class, 'question_id', 'id');
    }

    public function question_topic()
    {
        return $this->hasMany(QuestionTopic::class, 'question_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function sub_subcategory()
    {
        return $this->belongsTo(SubSubCategory::class, 'sub_subcategory_id');
    }

    public function scopeFilter($q)
    {
        $request = request();
        $search = $request->get('search');

        if (isset($search) && !empty($search)) {

            $q->where(function ($q) use ($search) {
                $q->orWhere('text_question', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%")
                    ->orWhere('question_type', 'LIKE', "%$search%")
                    ->orWhere('answer_type', 'LIKE', "%$search%")
                    ->orWhere('correct_answer_id', 'LIKE', "%$search%");
            });

        }

        if ($request->filled('category_id') && !empty($request->category_id)) {
            session()->put(['category_id' => $request->category_id]);
            $q->where('category_id', $request->category_id);
        } else {
            session()->forget('category_id');
        }

        if ($request->filled('sub_category_id') && !empty($request->sub_category_id)) {
            session()->put(['sub_category_id' => $request->sub_category_id]);
            $q->where('sub_category_id', $request->sub_category_id);
        } else {
            session()->forget('sub_category_id');
        }

        if ($request->filled('sub_subcategory_id') && !empty($request->sub_subcategory_id)) {
            session()->put(['sub_subcategory_id' => $request->sub_subcategory_id]);
            $q->where('sub_subcategory_id', $request->sub_subcategory_id);
        } else {
            session()->forget('sub_subcategory_id');
        }

        if ($request->filled('questions_topic_id') && !empty($request->questions_topic_id)) {
            session()->put(['questions_topic_id' => $request->questions_topic_id]);
            $q->whereHas('question_topic', function ($qq) use ($request) {
                $qq->where('topic_id', $request->questions_topic_id);
            });
        } else {
            session()->forget('questions_topic_id');
        }

        if ($request->filled('topic_id') && !empty($request->topic_id)) {
            $q->whereHas('question_topic', function ($qq) use ($request) {
                $qq->where('topic_id', $request->topic_id);
            });
        }

        if ($request->filled('section_id') && !empty($request->section_id)) {
            session()->put(['section_id' => $request->section_id]);
            $q->whereHas('exam_sections', function ($qq) use ($request) {
                $qq->where('section_id', $request->section_id);
            });
        } else {
            session()->forget('section_id');
        }

        if ($request->filled('exam_section_id') && !empty($request->exam_section_id)) {
            $q->whereHas('exam_sections', function ($qq) use ($request) {
                $qq->where('section_id', $request->exam_section_id);
            });
        }

    }

    public function scopeFilterApi($query)
    {
        $request = request();
        $query->where('status', 1);

        if ($request->filled('sub_categories') && !empty($request->sub_categories)) {
            $sub_categories = explode(',', $request->sub_categories);
            if (count($sub_categories) > 0) {
                $query->whereIn('sub_category_id', $sub_categories);
            }
        }
        if ($request->filled('sub_sub_categories') && !empty($request->sub_sub_categories)) {
            $sub_sub_categories = explode(',', $request->sub_sub_categories);
            if (count($sub_sub_categories) > 0) {
                $query->whereIn('sub_subcategory_id', $sub_sub_categories);
            }
        }
    }

    public static function modes()
    {
        return [
            'all' => [
                'name' => 'All',
                'desc' => 'All',
            ],

            'unused' => [
                'name' => 'Unused',
                'desc' => 'Unused',
            ],

            'used' => [
                'name' => 'Used',
                'desc' => 'Used',
            ],

            'correct' => [
                'name' => 'Correct',
                'desc' => 'Correct',
            ],

            'incorrect' => [
                'name' => 'InCorrect',
                'desc' => 'InCorrect',
            ],

            'marked' => [
                'name' => 'Marked',
                'desc' => 'Marked',
            ],

        ];
    }
}
