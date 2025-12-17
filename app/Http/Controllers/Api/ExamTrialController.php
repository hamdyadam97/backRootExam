<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NoQuestionsFoundException;
use App\Http\Requests\Api\ExamQuestionResultRequest;
use App\Http\Requests\Api\ExamQuestionSingleRequest;
use App\Http\Requests\Api\ExamTrialRequest;
use App\Http\Resources\Api\ExamTrialDetailResource;
use App\Http\Resources\Api\ExamTrialIndexResource;
use App\Http\Resources\Api\ExamTrialResource;
use App\Http\Resources\Api\SectionResource;
use App\Http\Resources\Api\ShowExamTrialResource;
use App\Http\Resources\Api\TopicResource;
use App\Models\ExamSection;
use App\Models\ExamTrail;
use App\Models\ExamTrialDetails;
use App\Models\QuestionsTopic;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Questions;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Api\QuestionResource;

class ExamTrialController extends BaseController
{

    public function get()
    {
        $user = \request()->user();
        $exams = ExamTrail::query()->where('user_id', $user->id)
            ->with(['categories'])
            ->orderByDesc('created_at')
            ->paginate(5);
        $data['exams'] = ExamTrialIndexResource::collection($exams);

        return $this->api_response(true, "Fetched Successfully", $data, 200, $this->getPaginatorData($exams));
    }

    public function single($exam_id)
    {
        $user = \request()->user();
        $exam = ExamTrail::query()->where('user_id', $user->id)
            ->with(['categories', 'subCategories', 'sections', 'topics'])->find($exam_id);

        if (!$exam) {
            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']]);
        }
        $data['exam'] = new ExamTrialResource($exam);

        $data['exam_report']['categories'] = $exam->getCategoryReport();
        $data['exam_report']['sub_categories'] = $exam->getSubCategoryReport();
        $data['exam_report']['sub_sub_categories'] = $exam->getSubSubCategoryReport();
        $data['exam_report']['sections'] = $exam->getSectionReport();
        $data['exam_report']['topics'] = $exam->getTopicsReport();

        return $this->api_response(true, "Fetched Successfully", $data);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $category_ids = $user->categories()->pluck('id')->toArray();

        $data['exam_modes'] = ExamTrail::getExamMode();
        $data['question_modes'] = Questions::modes();

        $data['sections'] = ExamSection::query()->whereIn('category_id', $category_ids)
            ->withCount([
                'questions' => function ($query) {
                    $query->filterApi();
                }
            ])
            ->orderBy('name')->get();
        $data['sections'] = SectionResource::collection($data['sections']);

        $data['topics'] = QuestionsTopic::query()->whereIn('category_id', $category_ids)
            ->withCount([
                'questions' => function ($query) {
                    $query->filterApi();
                }
            ])
            ->orderBy('topic')->get();
        $data['topics'] = TopicResource::collection($data['topics']);

        return $this->api_response(true, "Fetched Successfully", $data);
    }

    public function refreshSectionsAndTopics(Request $request)
    {
        $user = $request->user();
        $category_ids = $user->categories()->pluck('id')->toArray();

        $data['sections'] = ExamSection::query()->whereIn('category_id', $category_ids)
            ->withCount([
                'questions' => function ($query) {
                    $query->filterApi();
                }
            ])
            ->orderBy('name')->get();
        $data['sections'] = SectionResource::collection($data['sections']);

        $data['topics'] = QuestionsTopic::query()->whereIn('category_id', $category_ids)
            ->withCount([
                'questions' => function ($query) {
                    $query->filterApi();
                }
            ])
            ->orderBy('topic')->get();
        $data['topics'] = TopicResource::collection($data['topics']);

        return $this->api_response(true, "Fetched Successfully", $data);

    }

    public function store(ExamTrialRequest $request)
    {
        DB::beginTransaction();;
        try {
            $user = $request->user();

            $data = $request->only(ExamTrail::FILLABLE);
            $data['user_id'] = $user->id;
            $data['mode'] = $request->get('exam_mode', 'tatur');
            $exam = ExamTrail::query()->create($data);

            $exam->categories()->sync($request->get('categories', []));
            $exam->subCategories()->sync($request->get('sub_categories', []));
            $exam->subSubCategories()->sync($request->get('sub_sub_categories', []));
            $exam->sections()->sync($request->get('sections', []));
            $exam->topics()->sync($request->get('topics', []));

            $exam->saveDetails();

            DB::commit();;
            return $this->api_response(true, "Created Successfully", ['exam_id' => $exam->id]);

        } catch (NoQuestionsFoundException $exception) {
            DB::rollBack();;
            return $this->api_response(false, $exception->getMessage(), [], 500);
        } catch (\Exception $exception) {
            DB::rollBack();;
            return $this->api_response(false, $exception->getMessage(), [], 500);
        }
    }

    public function reset(Request $request)
    {
        $user = $request->user();
        $user->examtrails()->forceDelete();
        return $this->api_response(true, 'Exam data is cleared');
    }

    public function get_exam_questions(Request $request)
    {
        $user = $request->user();
        $exam_id = $request->get('exam_id', 0);
        $exam = ExamTrail::query()->where('user_id', $user->id)
            ->with(['questions', 'questions.questions_answers', 'details'])->find($exam_id);
//        'categories', 'subCategories', 'sections', 'topics',
        if (!$exam) {
            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']]);
        }


        $questions = $exam->questions->pluck('id')->toArray();
//        $questions = $exam->questions;//->pluck('id')->toArray();

        $data['exam'] = new ShowExamTrialResource($exam);
        $data['questions'] = $questions;
//        $data['questions'] = QuestionResource::collection($questions);
        $data['details'] = ExamTrialDetailResource::collection($exam->details);
        $data['lab_value'] = (new Setting())->valueOf('lab_value');
        return $this->api_response(true, 'Exam data', $data);
    }

    public function getQuestion($question_id)
    {
        $question = Questions::query()->find($question_id);
        $data['question'] = new QuestionResource($question);
        return $this->api_response(true, 'Exam data', $data);

    }

    public function store_question_answer($exam_id, ExamQuestionResultRequest $request)
    {
        $user = $request->user();

        $exam = ExamTrail::query()->where('user_id', $user->id)->find($exam_id);
        if (!$exam) {
            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']]);
        }

        $result = $request->get('result', []);

        foreach ($result as $single) {
            ExamTrialDetails::query()->where([
                'exam_trial_id' => $exam_id,
                'question_id' => $single['question_id'],
            ])->update([
                'answer_id' => $single['answer_id'],
                'is_correct' => $single['is_correct'],
                'is_marked' => $single['is_marked'],
            ]);
        }

        $data['total_questions'] = ExamTrialDetails::query()->where('exam_trial_id', $exam_id)->count();
        $data['correct_answers'] = ExamTrialDetails::query()->where('exam_trial_id', $exam_id)->where('is_correct', 1)->count();
        $data['wrong_answers'] = $data['total_questions'] - $data['correct_answers'];


        $exam->update($data);
        return $this->api_response(true, 'Exam complete successfully', $data);

    }

    public function store_single_question_answer($exam_id, ExamQuestionSingleRequest $request)
    {
        $user = $request->user();

        $exam = ExamTrail::query()->where('user_id', $user->id)->find($exam_id);
        if (!$exam) {
            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']]);
        }

        $result = $request->get('result', []);

        ExamTrialDetails::query()->where([
            'exam_trial_id' => $exam_id,
            'question_id' => $request['question_id'],
        ])->update([
            'answer_id' => $request['answer_id'],
            'is_correct' => $request['is_correct'],
            'is_marked' => $request['is_marked'],
        ]);

        $data['total_questions'] = ExamTrialDetails::query()->where('exam_trial_id', $exam_id)->count();
        $data['correct_answers'] = ExamTrialDetails::query()->where('exam_trial_id', $exam_id)->where('is_correct', 1)->count();
        $data['wrong_answers'] = $data['total_questions'] - $data['correct_answers'];


        $exam->update($data);
        return $this->api_response(true, 'Exam complete successfully', $data);

    }


}
