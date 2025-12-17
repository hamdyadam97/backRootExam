<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\User;
use Validator, Redirect, Response;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use DB;

class NotificationController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('notification.index');
    }

    public function get(Request $request)
    {

        $data = Notification::select('id','title','description');
        return datatables()::of($data)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function addupdate(Request $request)
    {
        // dd($request->description);
        if ($request->ajax()) {
            $loginUser = Auth::user();
            $rules = array(
                'title' => 'required',
                'description' => 'required',
            );
            if ($request->id) {
                $rules['title'] = 'required';
                $rules['description'] ='required';

            }
            $messsages = array(
                'title.required' => "The title field is required.",
                'description.required' => "The description field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Notification added successfully');
                if ($request->id) {
                    $model = Notification::where('id', $request->id)->first();
                    if ($model) {
                        $notification = $model;

                        $notification->title = $request->title;
                        $notification->description = $request->description;
                        $notification->save();
                        $succssmsg = trans('Notification updated successfully');
                        $result = ['status' => true, 'message' => $succssmsg, 'data' => []];

                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }

                } else {
                    //create
                    $notification = new Notification;

                    $notification->title = $request->title;
                    $notification->description = $request->description;

                    if ($notification->save()) {


                        $userlist = User::whereNotNull('device_id')->pluck('device_id')->toArray();
                        sendWebNotification($userlist,$request->title,$request->description);

                        $result = ['status' => true, 'message' => $succssmsg, 'data' => []];

                    }else {
                    $result = ['status' => false, 'message' => 'Error in saving data', 'data' => []];
                  }
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
            $user = Notification::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $user];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $user = Notification::where('id', $request->id)->first();
        if ($user->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }


}
