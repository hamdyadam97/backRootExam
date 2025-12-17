<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubsubcategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['categories'] = Category::with(['subCategories'])->active()->get();
        $data['subcategories'] = SubCategory::query()->active()->get();

        return view('sub_subcategory.index', $data);
    }

    public function get(Request $request)
    {
        $status = SubSubCategory::$status;
        $data = SubSubCategory::query()->with('subCategory')->filter();

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })->addColumn('subcategoryname', function ($row) use ($status) {
                return $row?->subCategory?->name ?? '-';
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
        die();
    }

    public function addupdate(Request $request)
    {

        if ($request->ajax()) {
                $rules['name'] = 'required|string|max:255';
                $rules['sub_cat_id'] = 'required';
                $rules['foreground_color'] = 'required';
                $rules['background_color'] = 'required';
            if ($request->id) {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif';
            } else {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif|required';
            }

            $messsages = array(
                'name.required' => "The name field is required.",
                'sub_cat_id.required' => "Please select Sub Category",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Sub Subcategory added successfully');
                if ($request->id) {
                    $model = SubSubCategory::where('id', $request->id)->first();
                    if ($model) {
                        $sub_subcategory = $model;
                        $succssmsg = trans('Sub Subcategory updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $sub_subcategory = new SubSubCategory;
                }
                $sub_subcategory->name = $request->name;
                $sub_subcategory->order = $request->order;
                $sub_subcategory->foreground_color = $request->foreground_color;
                $sub_subcategory->background_color = $request->background_color;
                $sub_subcategory->status = ($request->status == 1) ? 1 : 0;
                $sub_subcategory->sub_cat_id = $request->sub_cat_id;

                //icon upload code
                if ($request->hasFile('icon') && $request->icon) {
                    $dir = "sub_subcategory_icon/";

                    if ($sub_subcategory->icon) {
                        if (Storage::disk('local')->exists($dir . $sub_subcategory->icon)) {
                            Storage::delete($dir . $sub_subcategory->icon);
                        }
                    }
                    $extension = $request->file("icon")->getClientOriginalExtension();
                    $filename = uniqid() . "_" . time() . "." . $extension;
                    Storage::disk("local")->put($dir . $filename, \File::get($request->file("icon")));

                    $sub_subcategory->icon = $filename;
                }

                if ($sub_subcategory->save()) {
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
            $subsubcategory = SubSubCategory::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $subsubcategory];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $subsubcategory = SubSubCategory::where('id', $request->id)->first();

        if ($subsubcategory->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}
