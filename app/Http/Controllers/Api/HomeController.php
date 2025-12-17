<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\InstructorResource;
use App\Models\Blog;
use App\Models\Category;
use App\Models\ExamTrail;
use App\Models\Instructor;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HomeController extends BaseController
{

    public function landing()
    {
        $categories = Category::query()
            ->where('is_top', 1)
            ->where('status', 1)->get();
        $data['categories'] = CategoryResource::collection($categories);

        $instructors = Instructor::query()->get();
        $data['instructors'] = InstructorResource::collection($instructors);

        $blogs = Blog::query()->take(4)->with('category')->where('status', 1)->get();
        $data['blogs'] = BlogResource::collection($blogs);

        $data['default_image'] = asset('default.png');
        return $this->api_response(true, 'Done Successfully', $data);
    }

    public function home()
    {
        $user = User::query()->withCount([
            'subscriptions',
            'activeSubscriptions',
            'examtrails',
        ])->find(request()->user()->id);

        $data['total_subscriptions'] = $user['subscriptions_count'];
        $data['total_active_subscriptions'] = $user['active_subscriptions_count'];
        $data['categories_count'] = $user->categories_count();
        $data['sub_categories_count'] = $user->subcategoriesCount();
        $data['exams_count'] = $user['examtrails_count'];

//
        $data['exam_report']['categories'] = ExamTrail::categoryReport();
        $data['exam_report']['sub_categories'] = ExamTrail::subCategoryReport();
        $data['exam_report']['sub_sub_categories'] = ExamTrail::subSubCategoryReport();
        $data['exam_report']['sections'] = ExamTrail::sectionReport();
        $data['exam_report']['topics'] = ExamTrail::topicsReport();

        $data['categories_remaining_counts'] = ExamTrail::getAnsweredAndTotalQuestionsForCategories();
 //        $data['exam_report']['sub_sub_categories'] = ['title' => 'Categories', 'result' => []];;
//        $data['exam_report']['sections'] = ['title' => 'Categories', 'result' => []];;
//        $data['exam_report']['topics'] = ['title' => 'Categories', 'result' => []];;

        return $this->api_response(true, 'Done Successfully', $data);
    }

    // home page info api method
//    public function home_page_info(Request $request)
//    {
//        $categories = Category::select('categories.*', DB::raw('COUNT(exams.id) as total_exams'))
//            ->leftJoin('exams', function ($join) {
//                $join->on('categories.id', '=', 'exams.cat_id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->groupBy('categories.id')
//            ->get();
//
//        $latest_exams = Exams::select('exams.id as exam_id', 'exams.title as exam_title', 'exams.time as exam_time', 'categories.id as category_id', 'categories.name as category_title', DB::raw('COUNT(exam_questions.id) as total_questions'), 'categories.background_color', 'categories.foreground_color')
//            ->leftJoin('categories', function ($join) {
//                $join->on('exams.cat_id', '=', 'categories.id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->join('exam_questions', function ($join) {
//                $join->on('exams.id', '=', 'exam_questions.exam_id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->groupBy('exams.id')
//            ->orderByDesc('exams.id')
//            ->get();
//
//        $latest_users_exams = Userexams::where('user_id', $request->user()->id)
//            ->orderByDesc('id')
//            ->get();
//
//        $category_data = [];
//        $latest_exam_data = [];
//
//        //category data
//        foreach ($categories as $category) {
//            $category_data[] = new CategoryResource($category);
//        }
//
//        // latest new exams data
//        foreach ($latest_exams as $latest_exam) {
//            $latest_exam_data[] = new NewExamsResource($latest_exam);
//        }
//
//        // latest user exams data
//        $latest_user_exams_array = [];
//        foreach ($latest_users_exams as $latest_user_exam) {
//            $tmp = [];
//            $exam = Exams::find($latest_user_exam->exam_id);
//            if (empty($exam)) {
//                $tmp = [];
//            } else {
//                $tmp['last_user_exam_id'] = $exam->id;
//                $tmp['last_user_exam_title'] = $exam->title;
//                $tmp['last_user_exam_time'] = $exam->time;
//                $tmp['last_user_exam_score'] = $exam->score;
//            }
//            $latest_user_exams_array[] = $tmp;
//        }
//        return $this->send_response('Home page info', ['categories' => $category_data, 'last_user_exams' => $latest_user_exams_array, 'new_exams' => $latest_exam_data], 200);
//    }

    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'email' => 'required|email',
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        try {
            Mail::send([], [], function ($message) use ($request) {
                $body = "title : " . $request->title;
                $body .= " \n";
                $body .= "from email :" . $request->email;
                $body .= " \n";
                $body .= ' message : ' . $request->comment;

                $message->text($body); // for HTML rich messages.
                $message->subject('New Contact Message');
                $message->from('no-replay@rootsexam.ps', 'Roots Exam');
                $message->to('x');
            });

        } catch (\Exception $exception) {

        }

        return $this->api_response(true, 'Message Sent Successfully');

    }
}
