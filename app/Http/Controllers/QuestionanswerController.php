<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questionanswers;
use App\Models\Questions;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class QuestionanswerController extends Controller
{
    // public function __construct()
    // {
    //      $this->middleware('auth');
    // }

    // public function index()
    // {   
    //     $questions=Questions::where('deleted_at',null)->where('status',1)->get();
    //     return view('questionanswer.index',compact('questions'));
    // }

    // public function get(Request $request)
    // {

    //      $data = Questionanswers::leftjoin('questions','questions.id','=','questions_answers.question_id')
    //         ->select('questions_answers.*',DB::raw("group_concat(DISTINCT questions.text_question) as questionanswer"))
    //         ->groupBy('questions_answers.id');
    //         // ->where('questions_answers.deleted_at',null)
    //         // ->get();

    //     return datatables()::of($data)
    //         ->addIndexColumn()
                         
    //         ->filter(function ($instance) use ($request) {
    //             if (!empty($request->get('search')) && $request->get('search')) {
    //                     $search = $request->get('search');
    //                     $instance->where(function ($w) use ($search) {
    //                         $w->orWhere('questions_answers.answer', 'LIKE', "%$search%")
    //                         ->orWhere('questions.text_question', 'LIKE', "%$search%");
    //                     });
                    
    //             }
    //         })
    //         ->escapeColumns([])
    //         ->make(true);
    //     die();
    // }

    // public function addupdate(Request $request)
    // {
    //     if ($request->ajax()) {            
    //         if ($request->id) {
    //             $rules['question_id'] = 'required';
    //             $rules['answer'] = 'required';
    //         } else {
    //             $rules['question_id'] = 'required';
    //             $rules['answer'] = 'required';
    //         }

    //         $messsages = array(
    //             'question_id.required' => "Please select Question",
    //             'answer.required' => "The answer field is required.",
    //         );

    //         $validator = Validator::make($request->all(), $rules, $messsages);
    //         if ($validator->fails()) {
    //             $result = ['status' => false, 'error' => $validator->errors()];
    //         } else {
    //             $succssmsg = trans('Answer added successfully');
    //             if ($request->id) {
    //                 $model = Questionanswers::where('id', $request->id)->first();
    //                 if ($model) {
    //                     $answer = $model;
    //                     $succssmsg = trans('Answer updated successfully');
    //                 } else {
    //                     $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
    //                     return response()->json($result);
    //                 }
    //             } else {
    //                 $answer = new Questionanswers;
    //             }

    //             $answer->question_id = $request->question_id;
    //             $answer->answer = $request->answer;

    //             if ($answer->save()) {
    //                 $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
    //             } else {
    //                 $result = ['status' => false, 'message' => 'Error in saving data', 'data' => []];
    //             }
    //         }
    //     } else {
    //         $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
    //     }
    //     return response()->json($result);
    // }

    // public function detail(Request $request)
    // {
    //     $result = ['status' => false, 'message' => ""];
    //     if ($request->ajax()) {
    //         $answer = Questionanswers::find($request->id);
    //         $result = ['status' => true, 'message' => '', 'data' => $answer];
    //     }
    //     return response()->json($result);
    //     exit();
    // }

    // public function delete(Request $request)
    // {
    //     $answer = Questionanswers::where('id', $request->id)->first();
        
    //     if ($answer->delete()) {
    //         $result = ['status' => true, 'message' => trans('Delete successfully')];
    //     } else {
    //         $result = ['status' => false, 'message' => 'Delete fail'];
    //     }
    //     return response()->json($result);
    // }
}



