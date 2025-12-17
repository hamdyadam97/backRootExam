<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\ExamQuestionResultRequest;
use App\Http\Requests\Api\ExamTrialRequest;
use App\Http\Resources\Api\ExamTrialResource;
use App\Http\Resources\Api\SectionResource;
use App\Http\Resources\Api\TopicResource;
use App\Models\ExamSection;
use App\Models\ExamTrail;
use App\Models\ExamTrialDetails;
use App\Models\QuestionsTopic;
use Illuminate\Http\Request;
use App\Models\Userexams;
use App\Models\Exams;
use App\Models\Questions;
use App\Models\Userpackges;
use App\Models\Examquestions;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\Exam\ExamHistory;

//use App\Http\Resources\Exam\QuestionResource;
use App\Models\Questionanswers;
use App\Http\Resources\Api\QuestionResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\UserExamQuestionsAnswer;
use App\Models\UserExamTrial;
use Carbon\Carbon;

class ExamController extends BaseController
{

    public function get()
    {
        $user = \request()->user();
        $exams = ExamTrail::query()->where('user_id', $user->id)
            ->with(['categories', 'subCategories', 'sections', 'topics'])->get();
        $data['exams'] = ExamTrialResource::collection($exams);

        return $this->api_response(true, "Fetched Successfully", $data);
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

        return $this->api_response(true, "Fetched Successfully", $data);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $category_ids = $user->categories()->pluck('id')->toArray();

        $data['exam_modes'] = ExamTrail::getExamMode();
        $data['question_modes'] = Questions::modes();
        $data['sections'] = ExamSection::query()->whereIn('category_id', $category_ids)->withCount('questions')->get();
        $data['sections'] = SectionResource::collection($data['sections']);

        $data['topics'] = QuestionsTopic::query()->whereIn('category_id', $category_ids)->withCount('questions')->get();
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
            $exam = ExamTrail::query()->create($data);

            $exam->categories()->sync($request->get('categories', []));
            $exam->subCategories()->sync($request->get('sub_categories', []));
            $exam->sections()->sync($request->get('sections', []));
            $exam->topics()->sync($request->get('topics', []));

            $exam->saveDetails();

            DB::commit();;
            return $this->api_response(true, "Created Successfully");

        } catch (\Exception $exception) {
            DB::rollBack();;
            return $this->api_response(false, $exception->getMessage(), [], $exception->getCode());
        }
    }

    public function get_exam_questions(Request $request)
    {
        $user = $request->user();
        $exam_id = $request->get('exam_id', 0);
        $exam = ExamTrail::query()->where('user_id', $user->id)
            ->with(['categories', 'subCategories', 'sections', 'topics', 'questions', 'questions.questions_answers'])->find($exam_id);

        if (!$exam) {
            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']]);
        }


        $questions = $exam->questions;
        $data['exam'] = new ExamTrialResource($exam);
        $data['questions'] = QuestionResource::collection($questions);
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
            ]);
        }

        $data['total_questions'] = ExamTrialDetails::query()->where('exam_trial_id', $exam_id)->count();
        $data['correct_answers'] = ExamTrialDetails::query()->where('is_correct', true)->count();
        $data['wrong_answers'] = $data['total_questions'] - $data['correct_answers'];

        $exam->update($data);
        return $this->api_response(true, 'Exam complete successfully', $data);

//        $question = Questions::find($request->question_id);
//        if (empty($question)) {
//            return $this->send_error('Question not found', ['QuestionNotFound' => ['No question found for given question_id']], 400);
//        }
//        $req_ans_array = explode(',', $request->answer);
//        $act_ans_array = explode(',', $question->correct_answer_id);
//        $filtered_array = [];
//        $ans_status = false;
//        foreach ($req_ans_array as $value) {
//            if (!in_array($value, $act_ans_array)) {
//                $ans_status = false;
//                $filtered_array[] = $value;
//            }
//        }
//        if (empty($filtered_array)) {
//            $ans_status = true;
//        }
//        $exam = Exams::find($request->exam_id);
//        if (empty($exam)) {
//            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']], 400);
//        }
//        $check_exam_question = Examquestions::where('exam_id', $request->exam_id)->where('question_id', $request->question_id)->first();
//        if (empty($check_exam_question)) {
//            return $this->send_error('Question not available', ['QuestionNotAvailable' => ['This question is not available for current exam']], 400);
//        }
//        $user_exam = Userexams::where('user_id', $user_id)->where('exam_id', $request->exam_id)->where('id', $request->trial_id)->first();

//        $check_answer_already_submitted = UserExamQuestionsAnswer::where('exam_id', $request->exam_id)->where('question_id', $request->question_id)->where('user_exam_id', $request->trial_id)->where('user_id', $user_id)->first();
//        if (!empty($check_answer_already_submitted) && ($user_exam && $user_exam->status == 2)) {
//            return $this->send_error('Answer already submitted', ['AnswerAlreadySubmitted' => ['Answer already submitted for this question of current exam']], 400);
//        }
//
//        if (!empty($user_exam)) {
//            $store_new_answer = $user_exam;
//        } else {
//            $store_new_answer = new Userexams;
//        }
//        $store_new_answer->user_id = $user_id;
//        $store_new_answer->exam_id = $request->exam_id;
//        $store_new_answer->start_date = date('Y-m-d', strtotime($request->start_date));
//        $store_new_answer->end_date = date('Y-m-d', strtotime($request->end_date));
//        $store_new_answer->correct_answers = 0;
//        $store_new_answer->wrong_answers = 0;
//        $store_new_answer->score = 0;
//        if (!$store_new_answer->save()) {
//            return $this->send_error('Answer not saved', ['AnswerNotSaved' => ['Something went wrong while saving an answer']], 400);
//        }
//        $store_question_answer = new UserExamQuestionsAnswer;
//        if ($check_answer_already_submitted)
//            $store_question_answer = $check_answer_already_submitted;
//
//        $store_question_answer->user_id = $user_id;
//        $store_question_answer->user_exam_id = $request->trial_id;
//        $store_question_answer->exam_id = $request->exam_id;
//        $store_question_answer->question_id = $request->question_id;
//        $store_question_answer->answer = $request->answer;
//        $store_question_answer->answer_correct_status = ($ans_status) ? 1 : 0;
//        if (!$store_question_answer->save()) {
//            return $this->send_error('User exam question answer not saved', ['QuestionAnswerNotSaved' => ['Something went wrong while saving question answer']], 400);
//        }
//
//        $correct_answers = UserExamQuestionsAnswer::join('questions', 'questions.id', '=', 'user_exam_questions_answers.question_id')->where('questions.status', 1)->where('answer_correct_status', 1)->where('exam_id', $request->exam_id)->where('user_exam_id', $request->trial_id)->where('user_exam_questions_answers.user_id', $user_id)->count();
//        $wrong_answers = UserExamQuestionsAnswer::join('questions', 'questions.id', '=', 'user_exam_questions_answers.question_id')->where('questions.status', 1)->where('answer_correct_status', 0)->where('exam_id', $request->exam_id)->where('user_exam_id', $request->trial_id)->where('user_exam_questions_answers.user_id', $user_id)->count();
//
//        $tot_questions = Examquestions::join('questions', 'questions.id', '=', 'exam_questions.question_id')->where('questions.status', 1)->where('exam_id', $request->exam_id)->count();
//        $score = ($correct_answers * 100) / $tot_questions;
//
//        $store_new_answer->correct_answers = $correct_answers;
//        $store_new_answer->wrong_answers = $wrong_answers;
//        $store_new_answer->score = $score;
//        $store_new_answer->save();
//
//        $data = ['AnswerSaved' => ['Answer saved successfully']];
//        $data['trial_id'] = $request->trial_id;
//        $data['exam_score'] = $exam->score;
//        $data['total_questions'] = $tot_questions;
//        $data['total_answered_questions'] = $correct_answers + $wrong_answers;
//        $data['isPass'] = getUserPassStatus($exam->score, $store_new_answer->score);
//
//        $startTime = Carbon::parse($store_new_answer->created_at);
//        $endTime = Carbon::parse($store_new_answer->updated_at);
//        $totalDuration = $startTime->diff($endTime)->format('%H:%I:%S');
//        $data['time_used_for_exam'] = $totalDuration;
//
//        return $this->send_response('Answer saved', $data);
    }


//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
    // get all exams based
    public function get_exams(Request $request)
    {
        // $user = $request->user();
        $user = [];
        if ($request->bearerToken()) {
            $token = $request->bearerToken();
            $token_parts = explode('.', $token);
            $token_header = $token_parts[1];
            $token_header_json = base64_decode($token_header);
            $token_header_array = json_decode($token_header_json, true);
            if (isset($token_header_array['jti'])) {
                $user_token = $token_header_array['jti'];
                $user_id = DB::table('oauth_access_tokens')->where('id', $user_token)->first();
                if ($user_id)
                    $user = \App\User::find($user_id->user_id);
            }
        }

        $trialdata = [];
        if ($user)
            $trialdata = Userpackges::join('packages', 'packages.id', '=', 'user_packages.package_id')->join('package_exams', 'package_exams.package_id', '=', 'packages.id')->where('user_id', $user->id)->where('subscription_status', 1)->pluck('no_of_trial', 'package_exams.exam_id')->toArray();


        $exams_data = Exams::select('exams.id as exam_id', 'exams.title', 'exams.description', 'categories.name as category_name', 'sub_categories.name as sub_category_name', 'exams.icon as exam_icon', 'exams.time', 'exams.type', 'exams.status as exam_status', 'exams.score as exam_score', 'exams.cat_id as category', 'exams.sub_cat_id as subcategory', DB::raw('COUNT(exam_questions.id) as no_of_questions'), 'exams.hint', 'exams.show_hint', 'exams.show_answer', 'exams.video_link', 'exams.show_video', 'exams.time')
            ->leftjoin('categories', function ($join) {
                $join->on('exams.cat_id', '=', 'categories.id');
                $join->whereNull('categories.deleted_at');
            })
            ->leftjoin('sub_categories', function ($join) {
                $join->on('exams.sub_cat_id', '=', 'sub_categories.id');
                $join->whereNull('sub_categories.deleted_at');
            })
            ->leftJoin('exam_questions', function ($join) {
                $join->on('exam_questions.exam_id', '=', 'exams.id');
                $join->whereNull('exam_questions.deleted_at');
            })
            ->join('questions', function ($join) {
                $join->on('exam_questions.question_id', '=', 'questions.id');
                $join->where('questions.status', 1);
                $join->whereNull('questions.deleted_at');
            })
            ->when($user, function ($query) use ($trialdata) {
                $ids = array_keys($trialdata);
                $query->whereIn('exams.id', $ids);
            });
        $exams_data = $exams_data->Where('exams.status', 1);

        if (!empty($request->exam_id)) {
            $exams_data = $exams_data->Where('exams.id', $request->exam_id);
        }
        if (!empty($request->cat_id)) {
            $exams_data = $exams_data->Where('categories.id', $request->cat_id);
        }
        if (!empty($request->sub_cat_id)) {
            $exams_data = $exams_data->Where('sub_categories.id', $request->sub_cat_id);
        }
        if (!empty($request->type)) {
            $exams_data = $exams_data->Where('exams.type', $request->type);
        }
        if (!empty($request->search)) {
            $exams_data = $exams_data->Where(function ($q) use ($request) {
                $q->orWhere('categories.name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('sub_categories.name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('title', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }
        $exams_data = $exams_data->orderBy('exams.order', 'asc')->groupBy('exams.id')->get();

        $all_data = [];
        if ($exams_data->count() == 0) {
            return $this->send_response('Exams data', ['exams' => $all_data]);
        }


        foreach ($exams_data as $exam) {
            $trail_count = 0;
            if ($user)
                $trail_count = Userexams::where('exam_id', $exam->exam_id)->where('user_id', $user->id)->get()->count();
            $all_data[] = [
                'exam_id' => $exam->exam_id,
                'category' => [
                    'category_id' => $exam->category,
                    'category_name' => $exam->category_name
                ],
                'subcategory' => [
                    'subcategory_id' => $exam->subcategory,
                    'subcategory_name' => $exam->sub_category_name
                ],
                'title' => $exam->title,
                'description' => $exam->description,
                'exam_icon' => !empty($exam->exam_icon) ? asset('storage/exam_icon') . '/' . $exam->exam_icon : null,
                'time' => $exam->time,
                'type' => $exam->type,
                'trail_count' => $trail_count,
                'allowed_trail' => (isset($trialdata[$exam->exam_id])) ? $trialdata[$exam->exam_id] : 0,
                'score' => $exam->exam_score,
                'number_of_questions' => $exam->no_of_questions
            ];
        }
        return $this->send_response('Exams data', ['exams' => $all_data]);
    }

    public function getQuestion()
    {
        $data['question'] = Questions::query()->first();
//        dd($data);
        $html = view('api.question',$data)->render();
//        dd($html);
        return view('api.question',$data);
    }

    // store answer of questions
//    public function store_question_answer(Request $request)
//    {
//        $user_id = $request->user()->id;
//        $rules = array(
//            'question_id' => 'required',
//            'answer' => 'required|string',
//            'exam_id' => 'required',
//            'trial_id' => 'required',
//            'start_date' => 'required|date',
//            'end_date' => 'required|date|after:start_date',
//        );
//        $validator = Validator::make($request->all(), $rules);
//        if ($validator->fails()) {
//            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
//        }
//        $question = Questions::find($request->question_id);
//        if (empty($question)) {
//            return $this->send_error('Question not found', ['QuestionNotFound' => ['No question found for given question_id']], 400);
//        }
//        $req_ans_array = explode(',', $request->answer);
//        $act_ans_array = explode(',', $question->correct_answer_id);
//        $filtered_array = [];
//        $ans_status = false;
//        foreach ($req_ans_array as $value) {
//            if (!in_array($value, $act_ans_array)) {
//                $ans_status = false;
//                $filtered_array[] = $value;
//            }
//        }
//        if (empty($filtered_array)) {
//            $ans_status = true;
//        }
//        $exam = Exams::find($request->exam_id);
//        if (empty($exam)) {
//            return $this->send_error('Exam not found', ['ExamNotFound' => ['No exam found for given exam_id']], 400);
//        }
//        $check_exam_question = Examquestions::where('exam_id', $request->exam_id)->where('question_id', $request->question_id)->first();
//        if (empty($check_exam_question)) {
//            return $this->send_error('Question not available', ['QuestionNotAvailable' => ['This question is not available for current exam']], 400);
//        }
//        $user_exam = Userexams::where('user_id', $user_id)->where('exam_id', $request->exam_id)->where('id', $request->trial_id)->first();
//
//        $check_answer_already_submitted = UserExamQuestionsAnswer::where('exam_id', $request->exam_id)->where('question_id', $request->question_id)->where('user_exam_id', $request->trial_id)->where('user_id', $user_id)->first();
//        if (!empty($check_answer_already_submitted) && ($user_exam && $user_exam->status == 2)) {
//            return $this->send_error('Answer already submitted', ['AnswerAlreadySubmitted' => ['Answer already submitted for this question of current exam']], 400);
//        }
//
//        if (!empty($user_exam)) {
//            $store_new_answer = $user_exam;
//        } else {
//            $store_new_answer = new Userexams;
//        }
//        $store_new_answer->user_id = $user_id;
//        $store_new_answer->exam_id = $request->exam_id;
//        $store_new_answer->start_date = date('Y-m-d', strtotime($request->start_date));
//        $store_new_answer->end_date = date('Y-m-d', strtotime($request->end_date));
//        $store_new_answer->correct_answers = 0;
//        $store_new_answer->wrong_answers = 0;
//        $store_new_answer->score = 0;
//        if (!$store_new_answer->save()) {
//            return $this->send_error('Answer not saved', ['AnswerNotSaved' => ['Something went wrong while saving an answer']], 400);
//        }
//        $store_question_answer = new UserExamQuestionsAnswer;
//        if ($check_answer_already_submitted)
//            $store_question_answer = $check_answer_already_submitted;
//
//        $store_question_answer->user_id = $user_id;
//        $store_question_answer->user_exam_id = $request->trial_id;
//        $store_question_answer->exam_id = $request->exam_id;
//        $store_question_answer->question_id = $request->question_id;
//        $store_question_answer->answer = $request->answer;
//        $store_question_answer->answer_correct_status = ($ans_status) ? 1 : 0;
//        if (!$store_question_answer->save()) {
//            return $this->send_error('User exam question answer not saved', ['QuestionAnswerNotSaved' => ['Something went wrong while saving question answer']], 400);
//        }
//
//        $correct_answers = UserExamQuestionsAnswer::join('questions', 'questions.id', '=', 'user_exam_questions_answers.question_id')->where('questions.status', 1)->where('answer_correct_status', 1)->where('exam_id', $request->exam_id)->where('user_exam_id', $request->trial_id)->where('user_exam_questions_answers.user_id', $user_id)->count();
//        $wrong_answers = UserExamQuestionsAnswer::join('questions', 'questions.id', '=', 'user_exam_questions_answers.question_id')->where('questions.status', 1)->where('answer_correct_status', 0)->where('exam_id', $request->exam_id)->where('user_exam_id', $request->trial_id)->where('user_exam_questions_answers.user_id', $user_id)->count();
//
//        $tot_questions = Examquestions::join('questions', 'questions.id', '=', 'exam_questions.question_id')->where('questions.status', 1)->where('exam_id', $request->exam_id)->count();
//        $score = ($correct_answers * 100) / $tot_questions;
//
//        $store_new_answer->correct_answers = $correct_answers;
//        $store_new_answer->wrong_answers = $wrong_answers;
//        $store_new_answer->score = $score;
//        $store_new_answer->save();
//
//        $data = ['AnswerSaved' => ['Answer saved successfully']];
//        $data['trial_id'] = $request->trial_id;
//        $data['exam_score'] = $exam->score;
//        $data['total_questions'] = $tot_questions;
//        $data['total_answered_questions'] = $correct_answers + $wrong_answers;
//        $data['isPass'] = getUserPassStatus($exam->score, $store_new_answer->score);
//
//        $startTime = Carbon::parse($store_new_answer->created_at);
//        $endTime = Carbon::parse($store_new_answer->updated_at);
//        $totalDuration = $startTime->diff($endTime)->format('%H:%I:%S');
//        $data['time_used_for_exam'] = $totalDuration;
//
//        return $this->send_response('Answer saved', $data);
//    }

    public function getExamStatus(Request $request)
    {
        $user_id = $request->user()->id;
        $tot_questions = Examquestions::join('questions', 'questions.id', '=', 'exam_questions.question_id')->whereNull('questions.deleted_at')->where('questions.status', 1)->where('exam_id', $request->exam_id)->count();
        $answered = UserExamQuestionsAnswer::join('questions', 'questions.id', '=', 'user_exam_questions_answers.question_id')->whereNull('questions.deleted_at')->where('questions.status', 1)->where('exam_id', $request->exam_id)->where('user_exam_questions_answers.user_id', $user_id)->where('user_exam_id', $request->trial_id)->count();
        $response['total_questions'] = $tot_questions;
        $response['total_answered_questions'] = $answered;

        return $this->send_response('Answer saved', $response);

    }

    public function getExamReport(Request $request)
    {
        $user_exams = Userexams::find($request->trial_id);
        $user_id = $request->user()->id;
        $answered = UserExamQuestionsAnswer::join('questions', 'questions.id', '=', 'user_exam_questions_answers.question_id')->whereNull('questions.deleted_at')->where('questions.status', 1)->where('user_exam_questions_answers.user_id', $user_id)->where('user_exam_id', $request->trial_id)->get();
        $tot_questions = Examquestions::join('questions', 'questions.id', '=', 'exam_questions.question_id')->whereNull('questions.deleted_at')->where('questions.status', 1)->where('exam_id', $user_exams->exam_id)->count();

        $response['total_questions'] = $tot_questions;
        $response['total_answered_questions'] = $answered->count();
        $response['exam_id'] = $user_exams->exam_id;

        $startTime = Carbon::parse($user_exams->created_at);
        $endTime = Carbon::parse($user_exams->updated_at);
        $totalDuration = $startTime->diff($endTime)->format('%H:%I:%S');
        $response['time_used_for_exam'] = $totalDuration;

        $exam_data = Exams::select('id', 'title', 'score')->where('id', $user_exams->exam_id)->first();

        $response['isPass'] = getUserPassStatus($exam_data->score, $user_exams->score);
        $response['exams_score'] = $exam_data->score;
        $response['user_score'] = $user_exams->score;

        return $this->send_response('Exam Report', $response);

    }


    // user exam summary api
    public function user_exams_summary(Request $request)
    {
        $user_exams = Userexams::select(DB::raw('GROUP_CONCAT(id) as ids'), 'user_id', DB::raw('GROUP_CONCAT(score) as scores'), DB::raw('GROUP_CONCAT(user_exams_date) as user_exams_dates'), DB::raw('GROUP_CONCAT(exam_id) as exam_ids'), DB::raw('GROUP_CONCAT(correct_answers) as user_correct_answers'), DB::raw('GROUP_CONCAT(wrong_answers) as user_wrong_answers'))
            ->groupBy('user_id')
            ->where('user_id', $request->user()->id)
            ->get();

        if (empty($user_exams)) {
            return $this->send_response('User exams summary', ['user_exams_summary' => []]);
        }

        $user_exams_summary_data = [];
        foreach ($user_exams as $user_exam) {
            $tmp = [];
            $user = $request->user();
            if (empty($user)) {
                $tmp['user'][] = [];
            } else {
                $tmp['user'][] = new UserResource($user);
            }
            $exam_ids = (isset($user_exam->exam_ids)) ? explode(",", $user_exam->exam_ids) : "";
            $ids = (isset($user_exam->ids)) ? explode(",", $user_exam->ids) : "";
            $scores = (isset($user_exam->scores)) ? explode(",", $user_exam->scores) : "";
            $user_exams_dates = (isset($user_exam->user_exams_dates)) ? explode(",", $user_exam->user_exams_dates) : "";
            $correct_answers = (isset($user_exam->user_correct_answers)) ? explode(",", $user_exam->user_correct_answers) : 0;
            $wrong_answers = (isset($user_exam->user_wrong_answers)) ? explode(",", $user_exam->user_wrong_answers) : 0;
            foreach ($exam_ids as $key1 => $exam_id) {
                $exam = Exams::find($exam_id);
                if (empty($exam)) {
                    $tmp['exams'][] = [];
                } else {
                    $obj = new ExamResource($exam);
                    $obj_array = $obj->toArray(true);
                    $obj_array['user_exams_id'] = (isset($ids[$key1])) ? $ids[$key1] : "";
                    $obj_array['correct_answers'] = (isset($correct_answers[$key1])) ? $correct_answers[$key1] : 0;
                    $obj_array['wrong_answers'] = (isset($wrong_answers[$key1])) ? $wrong_answers[$key1] : 0;
                    $obj_array['user_exams_score'] = (isset($scores[$key1])) ? $scores[$key1] : "";
                    $obj_array['user_exams_date'] = (isset($user_exams_dates[$key1])) ? $user_exams_dates[$key1] : "";
                    $tmp['exams'][] = $obj_array;
                }
            }
            $user_exams_summary_data[] = $tmp;
        }
        return $this->send_response('User exams summary', ['user_exams_summary' => $user_exams_summary_data]);
    }

    // user exam status update
    public function change_user_exam_status(Request $request)
    {
        $user_id = $request->user()->id;
        $rules = array(
            'exam_id' => 'required',
            'status' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }
        if ($request->status == 1)
            $user_exams_data = new Userexams;
        else
            $user_exams_data = Userexams::where('user_id', $user_id)->where('exam_id', $request->exam_id)->first();

        $user_exams_data->user_id = $user_id;
        $user_exams_data->exam_id = $request->exam_id;
        $user_exams_data->status = $request->status;
        if (!$user_exams_data->save()) {
            return $this->send_error('Status not updated.', ['StatusNotUpdated' => ['Something went wrong while saving updating a user exam status']], 400);
        }
        $data = ['StatusUpdated' => ['User exam status updated successfully']];
        $data['trial_id'] = $user_exams_data->id;

        return $this->send_response('User exam status updated', $data);
    }


    public function questionanswerhistory(Request $request)
    {
        $user = $request->user();
        $query = Userexams::select('*', 'user_exams.score as user_exams_score', 'exams.score as exam_score', 'user_exams.id as id')
            ->join('exams', 'exams.id', '=', 'user_exams.exam_id')
            ->where('user_id', $user->id)
            ->orderBy('exams.order', 'asc')
            ->groupBy('user_exams.id');


        if (!empty($request->exam_id)) {
            $query = $query->where('exams.id', $request->exam_id);
        }
        if (!empty($request->trial_id)) {
            $query->where('user_exams.id', $request->trial_id);
        }

        $user_exams = $query->get();
        $array = [];
        foreach ($user_exams as $key => $value) {
            $obj = new ExamHistory($value);
            $obj_array = $obj->toArray(true);

            $array[$key] = $obj_array;
            $array[$key]['trial_id'] = $value->id;
            $array[$key]['user_exam_status'] = $value->status;
            $array[$key]['user_exam_status_label'] = Userexams::$status[$value->status];
            $array[$key]['user_exams_score'] = $value->user_exams_score;
            $array[$key]['exam_score'] = $value->exam_score;
            $array[$key]['total_exam_questions'] = 0;
            $array[$key]['isPass'] = getUserPassStatus($array[$key]['exam_score'], $array[$key]['user_exams_score']);

            $exam_questions = DB::select('select `questions`.*, `user_exam_questions_answers`.`answer` as `user_selected_answer`,`exam_section`.`name` as section_title from `exam_questions`
                    inner join `questions` on `questions`.`id` = `exam_questions`.`question_id`
                    inner join `exam_section` on `exam_section`.`id` = `questions`.`section_id`
                    left join `user_exam_questions_answers` on `user_exam_questions_answers`.`exam_id` = `exam_questions`.`exam_id` and `user_exam_questions_answers`.`question_id` = questions.id and `user_exam_questions_answers`.`user_exam_id` = ? and `user_exam_questions_answers`.`user_id` = ? where questions.status=1 and questions.deleted_at IS NULL and `exam_questions`.`exam_id` = ? group by `exam_questions`.`question_id` order by exam_questions.exam_question_sort_order asc', [$value->id, $user->id, $value->exam_id]);

            // print_r($exam_questions); die;

            $exam_questions_data_tmp = [];
            foreach ($exam_questions as $question) {
                $question->exam_id = $value->exam_id;
                $exam_questions_data_tmp[$question->section_id][] = new QuestionResource($question);
            }
            $exam_questions_data = [];
            $check = array();
            $index = 0;
            $count = 0;
            foreach ($exam_questions as $question) {
                if (!in_array($question->section_id, $check)) {
                    $check[] = $question->section_id;
                    $exam_questions_data[$index]['section_id'] = $question->section_id;
                    $exam_questions_data[$index]['section_title'] = $question->section_title;
                    if (isset($exam_questions_data_tmp[$question->section_id]))
                        $exam_questions_data[$index]['question_list'] = $exam_questions_data_tmp[$question->section_id];
                    else
                        $exam_questions_data[$index]['question_list'] = [];

                    $index++;
                    $count += count($exam_questions_data_tmp[$question->section_id]);
                }
            }
            $array[$key]['section_list'] = $exam_questions_data;
            $array[$key]['total_exam_questions'] = $count;

        }
        return $this->send_response('Exam data', $array);
        pr($user_exams);
    }

    public function userExamTrials(Request $request)
    {
        $rules = array(
            'exam_id' => 'required',
            'subscription_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        $user_id = $request->user()->id;

        $userExamTrial = new UserExamTrial();
        $userExamTrial->user_id = $user_id;
        $userExamTrial->exam_id = $request->exam_id;
        $userExamTrial->subscription_id = $request->subscription_id;
        $userExamTrial->date = date('Y-m-d');

        if (!$userExamTrial->save()) {
            return $this->send_error('User exam trial not saved', 400);
        }

        return $this->send_response('User exam trial save', $userExamTrial);
    }

    public function userExamPdf(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        if (empty($user_id)) {
            return $this->send_error('Missing required parameter', ['MissingUserId' => ['Missing parameter user_id']], 403);
        }
        $rules = array(
            'exam_id' => 'required',
            'trial_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        $pdfLink = getExamPdf($user_id, $request->exam_id, $request->trial_id, $request->new ? true : false);
        if ($pdfLink == '') {
            return $this->send_error('You must submit exam first', 400);
        }
        return $this->send_response('File create successfully', $pdfLink);

    }
}
