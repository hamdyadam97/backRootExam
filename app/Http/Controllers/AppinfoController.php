<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appinfos;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class AppinfoController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    public function index()
    {   
        return view('appinfo.index');
    }

    public function get(Request $request)
    {
        $data = Appinfos::where('deleted_at',null)->orderByDesc('id');
        return datatables()::of($data)
            ->addIndexColumn()
                        
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                        $search = $request->get('search');
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere('ios_version', 'LIKE', "%$search%");
                            $w->orWhere('android_version', 'LIKE', "%$search%");
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
                $rules['ios_version'] = 'required';
                $rules['android_version'] = 'required';
            } else {
                $rules['ios_version'] = 'required';
                $rules['android_version'] = 'required';
            }

            $messsages = array(
                'ios_version.required' => "The ios version field is required.",
                'android_version.required' => "The android version field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Version added successfully');
                if ($request->id) {
                    $model = Appinfos::where('id', $request->id)->first();
                    if ($model) {
                        $appinfo = $model;
                        $succssmsg = trans('Version updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $appinfo = new Appinfos;
                }
                $appinfo->ios_version = $request->ios_version;
                $appinfo->android_version = $request->android_version;

                if ($appinfo->save()) {
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
            $appinfo = Appinfos::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $appinfo];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $appinfo = Appinfos::where('id', $request->id)->first();
        
        if ($appinfo->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}

