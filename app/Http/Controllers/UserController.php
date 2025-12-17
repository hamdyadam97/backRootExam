<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use  Redirect, Response;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('user.index');
    }

    public function get(Request $request)
    {

        // $data = User::where("id", "!=", Auth::user()->id)->get();
        $status = User::$status;
        $data = User::query()->where("users.id", "!=", Auth::user()->id)
            ->filter()->orderByDesc('id');

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('role_type', function ($row) {
                if ($row->role_type == 1) {
                    return 'Admin';
                }
                if ($row->role_type == 2) {
                    return 'User';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%")
                            ->orWhere('email', 'LIKE', "%$search%")
                            ->orWhere('status', 'LIKE', "%$search%");
                    });

                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function addupdate(Request $request)
    {
        $request = reArrangeteleinputData($request);
//        dd($request->all());

        if ($request->ajax()) {
            $rules = array(
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'role_type' => 'required',
                'mobile' => 'required|digits:12|numeric|unique:users,mobile,' . $request->id,
                // 'token' => 'required',
                // 'device_id' => 'required',
                'score' => 'required|numeric',
            );
            if ($request->id) {
                $rules['thumb'] = 'mimes:jpeg,jpg,png,gif';
                $rules['password'] = 'nullable|string|confirmed';

//                $rules['mobile'] ='required|digits:12|numeric|unique:users,mobile,'.$request->id;

            } else {
                $rules['thumb'] = 'mimes:jpeg,jpg,png,gif|required';
                $rules['password'] = 'required|string|confirmed';
                // $rules['email'] = ['required','string','email','unique:users,email'];
//               $rules['mobile'] = 'required|digits:10|numeric|unique:users';
            }
            $messsages = array(
                'first_name.required' => "The first name field is required.",
                'last_name.required' => "The last name field is required.",
                'role_type.required' => "Please select Type.",
                'mobile.required' => "The mobile field is required.",
                'mobile.numaric' => "Please enter valid number",
                'token.required' => "The token field is required.",
                'device_id.required' => "This field is required.",
                'score.required' => "This field is required.",
                'score.numaric' => "Please enter valid score",
                'thumb.required' => "This field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('User added successfully');
                if ($request->id) {
                    $model = User::where('id', $request->id)->first();
                    if ($model) {
                        $user = $model;
                        //image upload code
                        if (!empty($request->thumb)) {
                            $dir = "user_image/";
                            if ($user->thumb) {
                                Storage::delete($dir . $user->thumb);
                            }
                            $filename = "";
                            $extension = $request
                                ->file("thumb")
                                ->getClientOriginalExtension();
                            $filename = uniqid() . "_" . time() . "." . $extension;
                            Storage::disk("local")->put($dir . $filename, \File::get($request->file("thumb")));
                            $user->thumb = $filename;
                        }
                        $user->first_name = $request->first_name;
                        $user->last_name = $request->last_name;
                        // $user->email = $request->email;
                        $user->role_type = $request->role_type;

                        $user->mobile_country_code = $request->mobile_country_code;
                        $user->dial_code = $request->dial_code;
                        $user->mobile_number = $request->mobile_number;
                        $user->mobile = $request->mobile;

                        $user->token = $request->token;
                        $user->device_id = $request->device_id;
                        $user->score = $request->score;
                        $user->status = ($request->status == 1) ? 1 : 0;

                        if ($request->password) {
                            $user->password = Hash::make($request->password);
                        }

                        $user->save();

                        $succssmsg = trans('User updated successfully');
                        $result = ['status' => true, 'message' => $succssmsg, 'data' => []];

                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }

                } else {
                    //create
                    $user = new User;
                    //image upload code
                    $filename = "";
                    if ($request->thumb) {
                        $extension = $request
                            ->file("thumb")
                            ->getClientOriginalExtension();
                        $dir = "user_image/";
                        $filename = uniqid() . "_" . time() . "." . $extension;
                        Storage::disk("local")->put($dir . $filename, \File::get($request->file("thumb")));
                    }

                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    // $user->email = $request->email;
                    $user->role_type = $request->role_type;

                    $user->mobile_country_code = $request->mobile_country_code;
                    $user->dial_code = $request->dial_code;
                    $user->mobile_number = $request->mobile_number;
                    $user->mobile = $request->mobile;

                    $user->token = $request->token;
                    $user->device_id = $request->device_id;
                    $user->score = $request->score;
                    $user->status = ($request->status == 1) ? 1 : 0;
                    $user->thumb = $filename ? $filename : null;
                    $user->password = Hash::make($request->password);
                    if ($user->save()) {

                        $result = ['status' => true, 'message' => $succssmsg, 'data' => []];

                    } else {
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
            $user = User::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $user];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }

    public function verify(Request $request)
    {
        $user = User::query()->where('id', $request->id)->first();

        if ($user) {
            $user->update(['mobile_verified_at' => now()]);
            $result = ['status' => true, 'message' => trans('Verified successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Verification fail'];
        }
        return response()->json($result);
    }
}
