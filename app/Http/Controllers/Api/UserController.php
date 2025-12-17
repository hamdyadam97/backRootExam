<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserPasswordRequest;
use App\Http\Requests\Api\UserProfileRequest;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Contactus;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    // get user info
    public function get_user_info(Request $request)
    {
        $user_data = $request->user();
        $data['user'] = new UserResource($user_data);
        return $this->api_response(true, 'User found', $data);
    }

    // update user info
    public function update_user_info(UserProfileRequest $request)
    {
        $user = $request->user();

        if (empty($user)) {
            return $this->send_error('No user found', ['UserNotFound' => ['User does not exist']], 400);
        }

        $data = $request->all();
        if (!empty($request->thumb)) {
            $filename = "";
            $dir = "user_image/";
            if ($user->thumb) {
                Storage::delete($dir . $user->thumb);
            }
            $extension = $request->file("thumb")->getClientOriginalExtension();
            $filename = uniqid() . "_" . time() . "." . $extension;
            Storage::disk("local")->put($dir . $filename, \File::get($request->file("thumb")));
            $data['thumb'] = !empty($filename) ? $filename : null;
        }
        $user->update($data);
        $data['user'] = new UserResource($user);
        return $this->send_response('User updated successfully', $data);
    }


    public function update_user_password(UserPasswordRequest $request)
    {
        $user = $request->user();

        if (empty($user)) {
            return $this->send_error('No user found', ['PasswordError' => ['User does not exist']], 400);
        }

        if (password_verify($request->current_password, $user->password)) {
            $user->update(['password' => Hash::make($request->password)]);
            return $this->api_response(true,'Password changed successfully', []);
        } else {
            return $this->api_response(false,'Invalid current password', [],422 );
        }

    }


//    public function user_category_data(Request $request)
//    {
//        $user = $request->user();
//        $user_id = $user->id;
//
//        $user_category_data = Userexams::select('categories.*', DB::raw('COUNT(exams.id) as total_exams'))
//            ->join('exams', function ($join) {
//                $join->on('user_exams.exam_id', '=', 'exams.id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->join('categories', function ($join) {
//                $join->on('exams.cat_id', '=', 'categories.id');
//                $join->whereNull('categories.deleted_at');
//            })
//            ->where('user_exams.user_id', '=', $user_id)
//            ->groupBy('categories.id')
//            ->get();
//
//        if (empty($user_category_data)) {
//            return $this->send_response('User categories data', ['grades_categories' => []]);
//        }
//
//        $user_exam_category_data = [];
//        foreach ($user_category_data as $user_category) {
//            $tmp = [];
//            $tmp['user_category_id'] = $user_category->id;
//            $tmp['user_category_title'] = $user_category->name;
//            $tmp['user_total_exams'] = $user_category->total_exams;
//            $tmp['category_icon'] = !empty($user_category->icon) ? asset('storage/category_icon') . '/' . $user_category->icon : null;
//            $tmp['foreground_color'] = $user_category->foreground_color;
//            $tmp['background_color'] = $user_category->background_color;
//            $user_exam_category_data[] = $tmp;
//        }
//        return $this->send_response('User categories data', ['grades_categories' => $user_exam_category_data]);
//    }
//
//    public function user_category_exams_data(Request $request)
//    {
//        $user = $request->user();
//        $user_id = $user->id;
//        $cat_id = $request->cat_id;
//
//        $user_exams_category_data = Userexams::select('categories.*', DB::raw('COUNT(exams.id) as total_exams'), DB::raw('GROUP_CONCAT(exams.id) as exam_ids'), 'user_exams.score', 'user_exams.correct_answers', 'user_exams.wrong_answers')
//            ->join('exams', function ($join) {
//                $join->on('user_exams.exam_id', '=', 'exams.id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->join('categories', function ($join) {
//                $join->on('exams.cat_id', '=', 'categories.id');
//                $join->whereNull('categories.deleted_at');
//            })
//            ->when($cat_id, function ($query) use ($cat_id) {
//                $query->where('exams.cat_id', $cat_id);
//            })
//            ->where('user_exams.user_id', '=', $user_id)
//            ->groupBy('categories.id')
//            ->get();
//
//        if (empty($user_exams_category_data)) {
//            return $this->send_response('User category exams data', []);
//        }
//
//        $user_exams_category_data_array = [];
//        foreach ($user_exams_category_data as $user_category_exam_data) {
//            $tmp = [];
//            $tmp['user_category_id'] = $user_category_exam_data->id;
//            $tmp['user_category_title'] = $user_category_exam_data->name;
//            $tmp['user_total_exams'] = $user_category_exam_data->total_exams;
//            $tmp['background_color'] = $user_category_exam_data->background_color;
//            $tmp['foreground_color'] = $user_category_exam_data->foreground_color;
//            $tmp['user_category_exams'] = [];
//            if (!empty($user_category_exam_data->exam_ids)) {
//                $exams = explode(',', $user_category_exam_data->exam_ids);
//            }
//            if (!empty($exams)) {
//                foreach ($exams as $exam) {
//                    $user_category_exams_tmp = [];
//                    $exam_data = Exams::select('id', 'title', 'score')->where('id', $exam)->first();
//                    $exam_questions = Examquestions::where('exam_id', $exam)->count();
//                    $user_exams = Userexams::select('score', 'correct_answers', 'wrong_answers')->where('exam_id', $exam)->where('user_id', $user_id)->first();
//                    $user_category_exams_tmp['user_exam_id'] = $exam_data->id;
//                    $user_category_exams_tmp['user_category_id'] = $user_category_exam_data->id;
//                    $user_category_exams_tmp['user_exam_title'] = $exam_data->title;
//                    $user_category_exams_tmp['total_questions'] = (!empty($exam_questions)) ? $exam_questions : 0;
//                    $user_category_exams_tmp['user_exams_score'] = (!empty($user_exams->score)) ? $user_exams->score : 0;
//                    $user_category_exams_tmp['exams_score'] = $exam_data->score;
//                    $user_category_exams_tmp['correct_answers'] = (!empty($user_exams->correct_answers)) ? $user_exams->correct_answers : 0;
//                    $user_category_exams_tmp['wrong_answers'] = (!empty($user_exams->wrong_answers)) ? $user_exams->wrong_answers : 0;
//                    $user_category_exams_tmp['isPass'] = getUserPassStatus($user_category_exams_tmp['exams_score'], $user_category_exams_tmp['user_exams_score']);
//                    $tmp['user_category_exams'][] = $user_category_exams_tmp;
//                }
//            }
//            $user_exams_category_data_array[] = $tmp;
//        }
//        return $this->send_response('User category exams data', $user_exams_category_data_array);
//    }
//
//
//    public function user_exams_data(Request $request)
//    {
//        $user = $request->user();
//        $user_id = $user->id;
//        $exam_id = $request->exam_id;
//
//        $query = Userexams::select('user_exams.*', DB::raw('COUNT(exams.id) as total_exams'), DB::raw('GROUP_CONCAT(exams.id) as exam_ids'), 'user_exams.score', 'user_exams.correct_answers', 'user_exams.wrong_answers', 'user_exams.id', 'exams.cat_id')
//            ->join('exams', function ($join) {
//                $join->on('user_exams.exam_id', '=', 'exams.id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->where('user_exams.user_id', '=', $user_id)
//            ->groupBy('user_exams.id');
//
//        if ($exam_id)
//            $query = $query->where('exams.id', $exam_id);
//
//        $user_exams_category_data = $query->get();
//
//        if (empty($user_exams_category_data)) {
//            return $this->send_response('User exams data', []);
//        }
//
//        $user_exams_category_data_array = [];
//        $tmp = [];
//        foreach ($user_exams_category_data as $user_category_exam_data) {
//            if (!empty($user_category_exam_data->exam_ids)) {
//                $exams = explode(',', $user_category_exam_data->exam_ids);
//            }
//            if (!empty($exams)) {
//                foreach ($exams as $exam) {
//                    $user_category_exams_tmp = [];
//                    $exam_data = Exams::select('id', 'title', 'score')->where('id', $exam)->first();
//                    $exam_questions = Examquestions::where('exam_id', $exam)->count();
//                    $user_exams = $user_category_exam_data;
//                    $user_category_exams_tmp['trial_id'] = $user_category_exam_data->id;
//                    $user_category_exams_tmp['user_exam_id'] = $exam_data->id;
//                    $user_category_exams_tmp['user_category_id'] = $user_category_exam_data->cat_id;
//                    $user_category_exams_tmp['user_exam_title'] = $exam_data->title;
//                    $user_category_exams_tmp['total_questions'] = (!empty($exam_questions)) ? $exam_questions : 0;
//                    $user_category_exams_tmp['user_exams_score'] = (!empty($user_exams->score)) ? $user_exams->score : 0;
//                    $user_category_exams_tmp['exams_score'] = $exam_data->score;
//                    $user_category_exams_tmp['correct_answers'] = (!empty($user_exams->correct_answers)) ? $user_exams->correct_answers : 0;
//                    $user_category_exams_tmp['wrong_answers'] = (!empty($user_exams->wrong_answers)) ? $user_exams->wrong_answers : 0;
//                    $user_category_exams_tmp['isPass'] = getUserPassStatus($user_category_exams_tmp['exams_score'], $user_category_exams_tmp['user_exams_score']);
//                    $tmp[] = $user_category_exams_tmp;
//                }
//            }
//        }
//        $user_exams_category_data_array['user_exams'] = $tmp;
//        return $this->send_response('User exams data', $user_exams_category_data_array);
//    }
//
//    public function user_exams(Request $request)
//    {
//        $user = $request->user();
//        $user_id = $user->id;
//        $exam_id = $request->exam_id;
//
//        $query = Userexams::select('user_exams.*', DB::raw('COUNT(user_exams.id) as total_trials'), DB::raw('GROUP_CONCAT(exams.id) as exam_ids'), 'user_exams.score', 'user_exams.correct_answers', 'user_exams.wrong_answers', 'user_exams.id', 'exams.cat_id', 'exams.id', 'exams.title', 'exams.icon')
//            ->join('exams', function ($join) {
//                $join->on('user_exams.exam_id', '=', 'exams.id');
//                $join->whereNull('exams.deleted_at');
//            })
//            ->where('user_exams.user_id', '=', $user_id)
//            ->groupBy('exams.id');
//
//        if ($exam_id)
//            $query = $query->where('exams.id', $exam_id);
//
//        $user_exams_category_data = $query->get();
//
//        if (empty($user_exams_category_data)) {
//            return $this->send_response('User exams data', []);
//        }
//        $tmp = [];
//        foreach ($user_exams_category_data as $exam_data) {
//            $user_category_exams_tmp['user_exam_id'] = $exam_data->exam_id;
//            $user_category_exams_tmp['user_exam_title'] = $exam_data->title;
//            $user_category_exams_tmp['total_trials'] = $exam_data->total_trials;
//            $user_category_exams_tmp['exam_icon'] = !empty($exam_data->icon) ? asset('storage/exam_icon') . '/' . $exam_data->icon : null;
//            $tmp[] = $user_category_exams_tmp;
//        }
//        $user_exams_category_data_array['user_exams'] = $tmp;
//        return $this->send_response('User exams data', $user_exams_category_data_array);
//    }


    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->token()->revoke();
        }
        return $this->send_response('Logout successfully', [], 200);
    }

    public function contactus(Request $request)
    {

        $user = $request->user();
        $user_id = $user->id;
        if (empty($user)) {
            return $this->send_error('No user found', ['ContactusError' => ['User does not exist']], 400);
        }
        $rules = array(
            'title' => 'required|string',
            'description' => 'required|string',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        $model = new Contactus;
        $model->user_id = $user_id;
        $model->title = $request->title;
        $model->description = $request->description;
        $model->save();

        return $this->send_response('Saved successfully', [], 200);

    }

    public function sendOTP(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        if (empty($user)) {
            return $this->send_error('No user found', ['PasswordError' => ['User does not exist']], 400);
        }
        $rules = array(
            'current_password' => 'required|string',
            'new_password' => 'required|string',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->send_error('Validation Errors', json_decode($validator->errors(), true), 422);
        }

        if ($request->current_password == $request->new_password) {
            return $this->send_error('Password Error', ['PasswordError' => ['Current and New Password can not be same']], 400);
        }

        if (password_verify($request->current_password, $user->password)) {
            $user->update(['password' => Hash::make($request->new_password)]);
            return $this->send_response('Password changed successfully', ['user' => $user], 200);
        } else {
            return $this->send_error('Password Error', ['PasswordError' => ['Invalid current password']], 400);
        }

    }
}
