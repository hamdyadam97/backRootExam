<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exams;
use App\User;
use App\Models\Userexams;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class UserexamController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    public function index()
    {
        $users = User::select("id", DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
                    ->where('role_type', 2)
                    ->where('status',1)
                    ->where('deleted_at',null)
                    ->get();

        $exams=Exams::where('deleted_at',null)->where('status',1)->get();

        return view('userexam.index',compact('users','exams'));
    }

    public function get(Request $request)
    {
        $data = Userexams::leftjoin('users','users.id','=','user_exams.user_id')
            ->leftjoin('exams','exams.id','=','user_exams.exam_id')
            ->select('user_exams.*',DB::raw("CONCAT(first_name, ' ', last_name) as username"),DB::raw("group_concat(DISTINCT exams.title) as examtitle"),DB::raw('user_exams.status as status_number'))
            ->orderByDesc('id')
            ->groupBy('user_exams.id');

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('start_date', function ($row) {
                return getDateFormateView($row->start_date);
            })
            ->editColumn('end_date', function ($row) {
                return getDateFormateView($row->end_date);
            })
            ->editColumn('status', function ($row) {
                $status_name=Userexams::$status;
                $badge_class = "";
                if($row->status == 0){
                    $badge_class = "bg-primary";
                }elseif($row->status == 1){
                    $badge_class = "bg-success";
                }elseif($row->status == 2){
                    $badge_class = "bg-danger";
                }
                return '<h5><span class="badge '.$badge_class.'">'.$status_name[$row->status].'</span></h5>';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    if ((bool)strtotime($search)) {
                        $instance->whereRaw("DATE_FORMAT(user_exams.user_exams_date, '%d.%m.%Y') LIKE '%{$search}%'");
                    }else{
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$search%")
                            ->orWhere('user_exams.score', 'LIKE', "%$search%")
                            ->orWhere('exams.title', 'LIKE', "%$search%")
                            ->when(strtolower($search)=="new",function($q){
                                $q->orWhere('user_exams.status', 0);
                            })
                            ->when(strtolower($search)=="in progress" || strtolower($search)=="in",function($q){
                                $q->orWhere('user_exams.status', 1);
                            })
                            ->when(strtolower($search)=="submitted" || strtolower($search)=="sub",function($q){
                                $q->orWhere('user_exams.status', 2);
                            });
                        });
                    }
                }
            })
            ->escapeColumns([])
            ->rawColumns(['status'])
            ->make(true);
        die();
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {

            $rules['user_id'] = 'required';
            $rules['exam_id'] = 'required';
            $rules['score'] = 'required|numeric';
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date|after:start_date';
            $rules['status'] = 'required';
            $messsages = array(
                'user_id.required' => "Please select user",
                'exam_id.required' => "Please select exam",
                'score.required' => "The score field is required.",
                'score.numeric' => "Please enter valid score",
                'user_exams_date.required' => "The date field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('User exam added successfully');
                if ($request->id) {
                    $model = Userexams::where('id', $request->id)->first();
                    if ($model) {
                        $userexam = $model;
                        $succssmsg = trans('User exam updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $userexam = new Userexams;
                }
                $userexam->user_id = $request->user_id;
                $userexam->exam_id = $request->exam_id;
                $userexam->score = $request->score;
                $userexam->start_date = ($request->start_date) ? date('Y-m-d', strtotime($request->start_date)) : NULL;
                $userexam->end_date = ($request->end_date) ? date('Y-m-d', strtotime($request->end_date)) : NULL;
                $userexam->status = $request->status;

                if ($userexam->save()) {
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
            $userexam = Userexams::find($request->id);
            $userexam->start_date = date('d.m.Y', strtotime($userexam->start_date));
            $userexam->end_date = date('d.m.Y', strtotime($userexam->end_date));
            $result = ['status' => true, 'message' => '', 'data' => $userexam];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $userexam = Userexams::where('id', $request->id)->first();

        if ($userexam->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
    public function downloadPdf()
    {
        $pdfLink= getExamPdf(\request()->user_id,\request()->exam_id,\request()->trial_id,\request()->new?true:false);
        return redirect($pdfLink);
    }
}


