<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class SubcategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['categories'] = Category::query()->where('deleted_at', null)->where('status', 1)->get();

        return view('subcategory.index', $data);
    }

    public function get(Request $request)
    {
        $status = SubCategory::$status;
        $data = SubCategory::query()->with(['category'])
            ->orderByDesc('id')
            ->filter();

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('categoryname', function ($row) {
                return @$row?->category?->name;
            })->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('foreground_color', function ($row) {
                return '<div class="btn rounded-pill" style="background-color:' . $row->foreground_color . '; width: auto; height: auto;">' . $row->foreground_color . '</div>';
            })
            ->editColumn('background_color', function ($row) {
                return '<div class="btn rounded-pill" style="background-color:' . $row->background_color . '; width: auto; height: auto;color:inverse">' . $row->background_color . '</div>';
            })
            ->escapeColumns([])
            ->rawColumns(['foreground_color', 'background_color'])
            ->make(true);
    }

    public function addupdate(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $rules['name'] = 'required|string|max:255';
            $rules['cat_id'] = 'required';
            $rules['foreground_color'] = 'required';
            $rules['background_color'] = 'required';
            if ($request->id) {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif';
            } else {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif|required';
            }

            $messsages = array(
                'name.required' => "The name field is required.",
                'cat_id.required' => "Please select Category",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Subcategory added successfully');
                if ($request->id) {
                    $model = SubCategory::where('id', $request->id)->first();
                    if ($model) {
                        $subcategory = $model;
                        $succssmsg = trans('Subcategory updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $subcategory = new SubCategory;
                }
                $subcategory->name = $request->name;
                $subcategory->order = $request->order;
                $subcategory->foreground_color = $request->foreground_color;
                $subcategory->background_color = $request->background_color;
                $subcategory->status = ($request->status == 1) ? 1 : 0;
                $subcategory->cat_id = $request->cat_id;

                //icon upload code
                if ($request->hasFile('icon') && $request->icon) {
                    $dir = "subcategory_icon/";

                    if ($subcategory->icon) {
                        if (Storage::disk('local')->exists($dir . $subcategory->icon)) {
                            Storage::delete($dir . $subcategory->icon);
                        }
                    }
                    $extension = $request->file("icon")->getClientOriginalExtension();
                    $filename = uniqid() . "_" . time() . "." . $extension;
                    Storage::disk("local")->put($dir . $filename, \File::get($request->file("icon")));

                    $subcategory->icon = $filename;
                }

                if ($subcategory->save()) {
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
            $subcategory = SubCategory::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $subcategory];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $subcategory = SubCategory::where('id', $request->id)->first();

        if ($subcategory->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}

