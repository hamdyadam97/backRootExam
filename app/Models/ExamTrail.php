<?php

namespace App\Models;

use App\Exceptions\NoQuestionsFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ExamTrail extends Model
{
    use SoftDeletes;

    const FILLABLE = [
        'title', 'user_id', 'mode', 'is_timed_mode', 'question_mode', 'question_count',

        'total_questions', 'correct_answers', 'wrong_answers'
    ];

    protected $fillable = self::FILLABLE;


    public static function getExamMode()
    {
        return [
            'tatur' => 'Tutor Mode',
            'exam' => 'Exam Mode',
        ];
    }

    public function getMode(): string
    {
        return self::getExamMode()[$this->mode];
    }

    public function getQuestionMode(): string
    {
        return @Questions::modes()[$this->question_mode]['name'];
    }


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_exam_trails', 'exam_trail_id', 'category_id');
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'sub_categories_exam_trails', 'exam_trail_id', 'sub_category_id');
    }

    public function subSubCategories()
    {
        return $this->belongsToMany(SubSubCategory::class, 'sub_sub_categories_exam_trails', 'exam_trail_id', 'sub_sub_category_id');
    }

    public function sections()
    {
        return $this->belongsToMany(ExamSection::class, 'exam_trails_sections', 'exam_trail_id', 'section_id');
    }

    public function topics()
    {
        return $this->belongsToMany(QuestionsTopic::class, 'exam_trails_topics', 'exam_trail_id', 'topic_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Questions::class, 'exam_trial_details', 'exam_trial_id', 'question_id');
    }

    public function details()
    {
        return $this->hasMany(ExamTrialDetails::class, 'exam_trial_id');
    }

    public function getQuestionQuery($sub_categories, $sub_sub_categories, $sections, $topics)
    {
        $questions = Questions::query()
            ->where('status', 1)
            ->whereIn('category_id', $this->categories->pluck('id')->toArray());


        if (count($sub_categories)) {
            $questions->where(function ($query) use ($sub_categories) {
                $query->whereIn('sub_category_id', $sub_categories)->orWhereNull('sub_category_id');
            });
        }


        if (count($sub_sub_categories)) {
            $questions->where(function ($query) use ($sub_sub_categories) {
                $query->whereIn('sub_subcategory_id', $sub_sub_categories)->orWhereNull('sub_subcategory_id');
            });
        }


        if (count($sections)) {
            $questions->where(function ($query) use ($sections) {
                $query->whereHas('exam_sections', function ($q) use ($sections) {
                    $q->whereIn('section_id', $sections);
                })->orWhereDoesntHave('exam_sections');
            });
        }


        if (count($topics)) {
            $questions->where(function ($query) use ($topics) {
                $query->whereHas('question_topic', function ($q) use ($topics) {
                    $q->whereIn('topic_id', $topics);
                })->orWhereDoesntHave('question_topic');
            });
        }

        return $questions;

    }

    public function saveDetails()
    {
        $sub_categories = $this->subCategories->pluck('id')->toArray();
        $sub_sub_categories = $this->subSubCategories->pluck('id')->toArray();

        $sections = $this->sections->pluck('id')->toArray();
        $topics = $this->topics->pluck('id')->toArray();
        $questions = $this->getQuestionQuery($sub_categories, $sub_sub_categories, $sections, $topics);
        $user = request()->user();

        $credential_questions = $this->getQuestionQuery($sub_categories, $sub_sub_categories, $sections, $topics)
            ->distinct()
            ->pluck('id')->toArray();
        switch ($this->question_mode) {
            case "all":
                break;
            case "unused":
                $user_exams = self::query()->where('user_id', $user->id)->pluck('id')->toArray();
                $used_questions = UserTrialExamsQuestionsVW::query()
                    ->whereIn('id', $user_exams)
                    ->whereIn('question_id', $credential_questions)
                    ->distinct()->pluck('question_id')->toArray();

//                $used_questions = ExamTrialDetails::query()
//                    ->whereIn('exam_trial_id', $user_exams)
//                    ->whereIn('question_id', $credential_questions)
//                    ->pluck('question_id')->toArray();
                $questions->whereNotIn('id', $used_questions);
                break;
            case "used":
                $user_exams = self::query()->where('user_id', $user->id)->pluck('id')->toArray();
                $used_questions = UserTrialExamsQuestionsVW::query()
                    ->whereIn('id', $user_exams)
                    ->whereIn('question_id', $credential_questions)
                    ->distinct()->pluck('question_id')->toArray();

//
//                $used_questions = ExamTrialDetails::query()
//                    ->whereIn('exam_trial_id', $user_exams)
//                    ->whereIn('question_id', $credential_questions)
//                    ->pluck('question_id')->toArray();
                $questions->whereIn('id', $used_questions);
                break;
            case "correct":
                $user_exams = self::query()->where('user_id', $user->id)->pluck('id')->toArray();
                $correct_questions = UserTrialExamsQuestionsVW::query()
                    ->whereIn('id', $user_exams)
                    ->whereIn('question_id', $credential_questions)
                    ->where('is_correct', true)
                    ->distinct()->pluck('question_id')->toArray();

//
//                $correct_questions = ExamTrialDetails::query()
//                    ->whereIn('exam_trial_id', $user_exams)
//                    ->whereIn('question_id', $credential_questions)
//                    ->where('is_correct', true)
//                    ->pluck('question_id')->toArray();
                $questions->whereIn('id', $correct_questions);
                break;
            case "incorrect":
                $user_exams = self::query()->where('user_id', $user->id)->pluck('id')->toArray();
                $correct_questions = UserTrialExamsQuestionsVW::query()
                    ->whereIn('id', $user_exams)
                    ->whereIn('question_id', $credential_questions)
                    ->where('is_correct', false)
                    ->distinct()->pluck('question_id')->toArray();

//
//                $correct_questions = ExamTrialDetails::query()
//                    ->whereIn('exam_trial_id', $user_exams)
//                    ->whereIn('question_id', $credential_questions)
//                    ->where('is_correct', false)
//                    ->pluck('question_id')->toArray();
                $questions->whereIn('id', $correct_questions);
                break;
            case "marked":
                $user_exams = self::query()->where('user_id', $user->id)->pluck('id')->toArray();
                $correct_questions = ExamTrialDetails::query()
                    ->whereIn('id', $user_exams)
                    ->whereIn('question_id', $credential_questions)
                    ->where('is_marked', true)
                    ->pluck('question_id')->toArray();
                $questions->whereIn('id', $correct_questions);
                break;
        }
        $question_ids = $questions->take($this->question_count)->distinct()->pluck('id')->toArray();

        if (!count($question_ids)) {
            throw new NoQuestionsFoundException();
        }
        $this->questions()->sync($question_ids);

        if ($this->question_count > count($question_ids)) {
            $this->question_count = count($question_ids);
            $this->save();
        }
    }


    public function getCategoryReport()
    {
        $categories = $this->categories;
        $result = [];
        foreach ($categories as $category) {
            $total_questions = $this->questions->where('category_id', $category->id)->pluck('id')->toArray();

            $correct_answers = 0;
            if (count($total_questions)) {
                $correct_answers = $this->details()->whereIn('question_id', $total_questions)
                    ->where('is_correct', true)->count();

            }
            $result[] = [
                'id' => $category->id,
                'name' => $category->name,
                'total_questions' => count($total_questions),
                'correct_answers' => $correct_answers,
            ];
        }
        return ['title' => 'Categories', 'result' => $result];
    }

    public function getSubCategoryReport()
    {
        $sub_categories = $this->subCategories;
        $result = [];
        foreach ($sub_categories as $sub_category) {

            $total_questions = $this->questions->where('sub_category_id', $sub_category->id)->pluck('id')->toArray();

            $correct_answers = 0;
            if (count($total_questions)) {
                $correct_answers = $this->details()->whereIn('question_id', $total_questions)
                    ->where('is_correct', true)->count();

            }
            $result[] = [
                'id' => $sub_category->id,
                'name' => $sub_category->name,
                'total_questions' => count($total_questions),
                'correct_answers' => $correct_answers,
            ];
        }
        return ['title' => 'Sub Categories', 'result' => $result];
    }

    public function getSubSubCategoryReport()
    {
        $sub_sub_categories = $this->subSubCategories;
        $result = [];
        foreach ($sub_sub_categories as $sub_sub_category) {

            $total_questions = $this->questions->where('sub_subcategory_id', $sub_sub_category->id)->pluck('id')->toArray();

            $correct_answers = 0;
            if (count($total_questions)) {
                $correct_answers = $this->details()->whereIn('question_id', $total_questions)
                    ->where('is_correct', true)->count();

            }
            $result[] = [
                'id' => $sub_sub_category->id,
                'name' => $sub_sub_category->name,
                'total_questions' => count($total_questions),
                'correct_answers' => $correct_answers,
            ];
        }
        return ['title' => 'Sub SubCategories', 'result' => $result];
    }

    public function getSectionReport()
    {
        $sections = $this->sections;
        $result = [];
        foreach ($sections as $section) {

            $total_questions = $this->questions()->whereHas('exam_sections', function ($q) use ($section) {
                $q->where('section_id', $section->id);
            })->pluck('questions.id')->toArray();

            $correct_answers = 0;
            if (count($total_questions)) {
                $correct_answers = $this->details()->whereIn('question_id', $total_questions)
                    ->where('is_correct', true)->count();

            }
            $result[] = [
                'id' => $section->id,
                'name' => $section->name,
                'total_questions' => count($total_questions),
                'correct_answers' => $correct_answers,
            ];
        }
        return ['title' => 'Sections', 'result' => $result];
    }

    public function getTopicsReport()
    {
        $topics = $this->topics;
        $result = [];
        foreach ($topics as $topic) {

            $total_questions = $this->questions()->whereHas('question_topic', function ($q) use ($topic) {
                $q->where('topic_id', $topic->id);
            })->pluck('questions.id')->toArray();

            $correct_answers = 0;
            if (count($total_questions)) {
                $correct_answers = $this->details()->whereIn('question_id', $total_questions)
                    ->where('is_correct', true)->count();

            }
            $result[] = [
                'id' => $topic->id,
                'name' => $topic->topic,
                'total_questions' => count($total_questions),
                'correct_answers' => $correct_answers,
            ];
        }
        return ['title' => 'Topics', 'result' => $result];
    }

    public static function categoryReport()
    {
        $userId = auth('api')->id();
        $result = DB::select("
                                SELECT
                                    category_name AS name,
                                    category_id AS id,
                                    COUNT(DISTINCT question_id) AS total_questions,
                                    COALESCE(SUM(is_correct), 0) AS correct_answers
                                FROM
                                    user_trail_exams_questions
                                WHERE
                                    user_id = ?
                                GROUP BY
                                    category_name,
                                    category_id
                            ", [$userId]);

        return [
            'title' => 'Categories',
            'result' => $result,
        ];
    }


    public static function subCategoryReport()
    {
        $userId = auth('api')->id();

        $result = DB::select("
                                SELECT
                                    sub_category_name AS name,
                                    sub_category_id AS id,
                                    COUNT(DISTINCT question_id) AS total_questions,
                                    COALESCE(SUM(is_correct), 0) AS correct_answers
                                FROM
                                    user_trail_exams_questions
                                WHERE
                                    user_id = ?
                                and sub_category_id is not null
                                GROUP BY
                                    sub_category_name,
                                    sub_category_id
                            ", [$userId]);

        return [
            'title' => 'Sub Categories',
            'result' => $result,
        ];

    }

    public static function subSubCategoryReport()
    {
        $userId = auth('api')->id();
        $result = DB::select("
                                SELECT
                                    sub_sub_category_name AS name,
                                    sub_subcategory_id AS id,
                                    COUNT(DISTINCT question_id) AS total_questions,
                                    COALESCE(SUM(is_correct), 0) AS correct_answers
                                FROM
                                    user_trail_exams_questions
                                WHERE
                                    user_id = ?
                                and sub_subcategory_id is not null
                                GROUP BY
                                    sub_sub_category_name,
                                    sub_subcategory_id
                            ", [$userId]);


        return [
            'title' => 'Sub SubCategories',
            'result' => $result,
        ];
    }


    public static function sectionReport()
    {
        $userId = auth('api')->id();
        $result = DB::select("
                                SELECT
                                    section_name AS name,
                                    section_id AS id,
                                    COUNT(DISTINCT question_id) AS total_questions,
                                    COALESCE(SUM(is_correct), 0) AS correct_answers
                                FROM
                                    user_trail_exams_questions
                                WHERE
                                    user_id = ?
                                and section_id is not null
                                GROUP BY
                                    section_name,
                                    section_id
                            ", [$userId]);


        // Return the final result
        return [
            'title' => 'Sections',
            'result' => $result,
        ];
    }

    public static function topicsReport()
    {
        $userId = auth('api')->id();
        $result = DB::select("
                                SELECT
                                    topic_name AS name,
                                    topic_id AS id,
                                    COUNT(DISTINCT question_id) AS total_questions,
                                    COALESCE(SUM(is_correct), 0) AS correct_answers
                                FROM
                                    user_trail_exams_questions
                                WHERE
                                    user_id = ?
                                and topic_id is not null
                                GROUP BY
                                    topic_name,
                                    topic_id
                            ", [$userId]);

        return [
            'title' => 'Topics',
            'result' => $result,
        ];
    }


    public static function getAnsweredAndTotalQuestionsForCategories()
    {

        $user = auth('api')->user();
        $user_id = $user->id;
        $categories = $user->categories()->pluck('id')->toArray();
        if (empty($categories)) {
            return []; // or return a default structure / message
        }

        $placeholders = implode(',', array_fill(0, count($categories), '?'));

        $sql = "SELECT
                    c.id AS category_id,
                    c.name AS category_name,
                    COUNT(DISTINCT u.question_id) AS answered_questions,
                    COUNT(DISTINCT q.id) AS total_questions,
                    COUNT(DISTINCT q.id) - COUNT(DISTINCT u.question_id) AS remaining_questions
                FROM
                    categories c
                LEFT JOIN questions q ON q.category_id = c.id
                LEFT JOIN user_trail_exams_questions u ON u.question_id = q.id
                WHERE c.id IN ($placeholders)
                AND u.user_id = $user_id
                GROUP BY
                    c.id, c.name;
            ";


        return DB::select($sql, $categories);


    }

}
