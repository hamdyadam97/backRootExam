<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Examquestions;
use App\Models\SubCategory;
use App\Models\Exams;
use App\Models\SubSubCategory;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    public function index()
    {
        $categories=Category::where('deleted_at',null)->where('status',1)->get();

        return view('exam.index',compact('categories'));
    }

    public function get(Request $request)
    {
         $status = Exams::$status;
         $data = Exams::leftjoin('categories','categories.id','=','exams.cat_id')
            ->leftjoin('sub_categories','sub_categories.id','=','exams.sub_cat_id')
            ->leftjoin('sub_sub_categories','sub_sub_categories.id','=','exams.sub_sub_cat_id')
            ->select('exams.*',DB::raw("group_concat(DISTINCT categories.name) as categoryname"),DB::raw("group_concat(DISTINCT sub_categories.name) as subcategoryname"),DB::raw("group_concat(DISTINCT sub_sub_categories.name) as sub_subcategoryname"))
            ->orderByDesc('id')
            ->groupBy('exams.id');
            // ->where('exams.deleted_at',null)
            // ->get();

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status]:"";
            })
            ->editColumn('type', function($row){
                if($row->type == 1){
                    return 'SSC';
                }
                if($row->type == 2){
                    return 'HSC';
                }
            })
            ->editColumn('show_hint', function($row){
                return (!empty($row->show_hint) && $row->show_hint == 1)?'YES':'NO';
            })
            ->editColumn('show_answer', function($row){
                return (!empty($row->show_answer) && $row->show_answer == 1)?'YES':'NO';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('exams.title', 'LIKE', "%$search%")
                        ->orWhere('exams.type', 'LIKE', "%$search%")
                        ->orWhere('exams.score', 'LIKE', "%$search%")
                        ->orWhere('categories.name', 'LIKE', "%$search%")
                        ->orWhere('sub_categories.name', 'LIKE', "%$search%")
                        ->orWhere('sub_sub_categories.name', 'LIKE', "%$search%")
                        ->orWhere('exams.hint', 'LIKE', "%$search%")
                        ->orWhere('exams.order', 'LIKE', "%$search%")
                        ->orWhere('exams.video_link', 'LIKE', "%$search%")
                        ->when(strtolower($search)=="yes" || strtolower($search)=="y" ,function($q){
                            $q->orWhere('exams.show_hint', 1);
                            $q->orWhere('exams.show_answer', 1);
                        })
                        ->when(strtolower($search)=="no" || strtolower($search)=="n",function($q){
                            $q->orWhere('exams.show_hint', 0);
                            $q->orWhere('exams.show_answer', 0);

                        })
                        ->when(strtolower($search)=="active" || strtolower($search)=="act",function($q){
                            $q->orWhere('exams.status', 1);
                        })
                        ->when(strtolower($search)=="deactive" || strtolower($search)=="deact",function($q){
                            $q->orWhere('exams.status', 0);
                        });
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function getSubcategories(Request $request)
    {
        if ($request && !empty($request->id)) {

            $subcategories = Subcategory::where('cat_id', $request->id)->where('status',1)->where('deleted_at',null)->pluck('name', 'id');
            if (!empty($subcategories)) {
                $result = ['status' => true, 'message' => 'success', 'data' => $subcategories];
            }else{
                $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
            }
        }else{
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function getSubSubcategories(Request $request)
    {
        if ($request && !empty($request->id)) {

            $subSubcategories = SubSubCategory::where('sub_cat_id', $request->id)->where('status',1)->where('deleted_at',null)->pluck('name', 'id');
            if (!empty($subSubcategories)) {
                $result = ['status' => true, 'message' => 'success', 'data' => $subSubcategories];
            }else{
                $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
            }
        }else{
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function addupdate(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $rules['cat_id'] = 'required';
            $rules['sub_cat_id'] = 'required';
            $rules['sub_sub_cat_id'] = 'required';
            $rules['title'] = 'required';
            $rules['time'] = 'required';
            $rules['score'] = 'required|numeric';
            $rules['type'] = 'required';
            $rules['order'] = 'required';
            $rules['hint'] = 'required';
            $rules['show_hint'] = 'required';
            $rules['show_answer'] = 'required';
            $rules['video_link'] = 'nullable|url';
            if ($request->id) {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif';
            } else {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif|required';
            }

            $messsages = array(
                'cat_id.required' => "Please select Category",
                'sub_cat_id.required' => "Please select Subcategory",
                'sub_sub_cat_id.required' => "Please select Sub-Subcategory",
                'title.required' => "The title field is required.",
                'time.required' => "The time field is required.",
                'score.required' => "The score field is required.",
                'score.numeric' => "Please enter valid score",
                'type.required' => "The type field is required.",
                'description.required' => "The description field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Exam added successfully');
                if ($request->id) {
                    $model = Exams::where('id', $request->id)->first();
                    if ($model) {
                        $exam = $model;
                        $succssmsg = trans('Exam updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $exam = new Exams;
                }
                $exam->cat_id = $request->cat_id;
                $exam->sub_cat_id = $request->sub_cat_id;
                $exam->sub_sub_cat_id = $request->sub_sub_cat_id;
                $exam->title = $request->title;
                $exam->time = $request->time;
                $exam->score = $request->score;
                $exam->type = $request->type;
                $exam->status = ($request->status==1)? 1:0;
                $exam->description = $request->description;
                $exam->order = $request->order;
                $exam->hint = $request->hint;
                $exam->show_hint = $request->show_hint;
                $exam->show_answer = $request->show_answer;
                $exam->video_link = $request->video_link;

                //icon upload code
                if ($request->hasFile('icon') && $request->icon)
                {
                    $dir = "exam_icon/";

                    if($exam->icon) {
                        if(Storage::disk('local')->exists($dir . $exam->icon)) {
                            Storage::delete($dir . $exam->icon);
                        }
                    }
                    $extension = $request->file("icon")->getClientOriginalExtension();
                    $filename = uniqid() . "_" . time() . "." . $extension;
                    Storage::disk("local")->put($dir . $filename,\File::get($request->file("icon")));

                    $exam->icon = $filename;
                }

                if ($exam->save()) {
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
            $exam = Exams::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $exam];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $exam = Exams::where('id', $request->id)->first();

        if ($exam->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }

    public function copyExam(Request $request)
    {
        // dd($request->all());
        $exam = Exams::find($request->id);

        $filename = "";
        if(!empty($exam->icon)) {
            $dir = "exam_icon/";

            $extension = File::extension($exam->icon);
            $filename = uniqid() . "_" . time() . "." .$extension;
            Storage::copy($dir . $exam->icon, $dir . $filename);
        }

        $copyExam = $exam->replicate()->fill([
                'title'       => $exam->title.' - copy',
                'icon'        => $filename,
            ]);

        $copyExam->type = 2;
        $copyExam->show_hint = 1;
        $copyExam->show_answer = 1;
        $copyExam->status = 1;

        if($copyExam->save()) {

            $examQuestions = Examquestions::where('exam_id',$exam->id)->get();

            if(count($examQuestions) > 0) {
                foreach($examQuestions as $examQuestion) {
                    $question = new Examquestions();
                    $question->exam_id     = $copyExam->id;
                    $question->question_id = $examQuestion->question_id;
                    $question->save();
                }
            }

            $result = ['status' => true, 'message' => trans('Exam copy successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Copy fail'];
        }

        return response()->json($result);
    }
}

