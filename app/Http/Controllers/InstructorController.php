<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstructorRequest;
use App\Models\Instructor;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\File;
use Validator, Redirect, Response;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use DB;

class InstructorController extends Controller
{

    public function index()
    {
        return view('instructors.index');
    }

    public function get(Request $request)
    {

        // $data = User::where("id", "!=", Auth::user()->id)->get();
        $data = Instructor::query()->orderByDesc('id');

        return datatables()::of($data)
            ->addIndexColumn()
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('name', 'LIKE', "%$search%");
                    });

                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function addupdate(InstructorRequest $request)
    {
        if ($request->ajax()) {
            $succssmsg = trans('User added successfully');
            $model = null;
            if ($request->id) {
                $model = Instructor::query()->find($request->id);
                if (!$model) {
                    $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                    return response()->json($result);
                }
            }
            $data = $request->all();
            if (!empty($request->image)) {
                $dir = "instructor_image/";
                if (@$model->image) {
                    Storage::delete($dir . $model->image);
                }

                $extension = $request
                    ->file("image")
                    ->getClientOriginalExtension();
                $filename = uniqid() . "_" . time() . "." . $extension;
                Storage::disk("local")->put($dir . $filename, File::get($request->file("image")));
                $data['image'] = $filename;
            }

            if ($model) {
                $model->update($data);
            } else {
                Instructor::query()->create($data);
            }

            $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
        }

        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $user = Instructor::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $user];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $user = Instructor::query()->find($request->id);
        if ($user->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}
