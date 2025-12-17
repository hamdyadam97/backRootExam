<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examquestions;
use App\Models\Exams;
use App\Models\Questions;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class ExamquestionController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    public function index()
    {   
        $exams=Exams::where('deleted_at',null)->where('status',1)->get();
        $questions=Questions::where('deleted_at',null)->where('status',1)->get();

        return view('examquestion.index',compact('exams','questions'));
    }

    public function get(Request $request)
    {
         $data = Examquestions::leftjoin('exams','exams.id','=','exam_questions.exam_id')
            ->leftjoin('questions','questions.id','=','exam_questions.question_id')
            ->select('exam_questions.*',DB::raw("group_concat(DISTINCT exams.title) as examname"),DB::raw("group_concat(DISTINCT questions.text_question) as questionname"))
            ->orderByDesc('id')
            ->whereNull('questions.deleted_at')
            ->groupBy('exam_questions.id');

        return datatables()::of($data)
            ->addIndexColumn()
                                         
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                        $search = $request->get('search');
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere('exams.title', 'LIKE', "%$search%")
                            ->orWhere('questions.text_question', 'LIKE', "%$search%");
                        });
                    
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {            
            if ($request->id) {
                $rules['exam_id'] = 'required';
                $rules['question_id'] = 'required';
            } else {
                $rules['exam_id'] = 'required';
                $rules['question_id'] = 'required';
            }

            $messsages = array(
                'exam_id.required' => "Please select exam",
                'question_id.required' => "Please select question",

            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Exam question added successfully');
                if ($request->id) {
                    $model = Examquestions::where('id', $request->id)->first();
                    if ($model) {
                        $examquestion = $model;
                        $succssmsg = trans('Exam question updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $examquestion = new Examquestions;
                }
                $examquestion->exam_id = $request->exam_id;               
                $examquestion->question_id = $request->question_id;               

                if ($examquestion->save()) {
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => 'Error in saving data', 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $examquestion = Examquestions::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $examquestion];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $examquestion = Examquestions::where('id', $request->id)->first();
        
        if ($examquestion->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}


