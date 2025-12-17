<?php

namespace App\Http\Controllers;

use App\Exports\CategoryNestedExport;
use Illuminate\Http\Request;
use App\Models\Category;
use Auth;
use Validator;
use DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('category.index');
    }

    public function get(Request $request)
    {
        $status = Category::$status;
        $data = Category::query()->orderByDesc('id');
        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('foreground_color', function ($row) {
                return '<div class="btn rounded-pill" style="background-color:' . $row->foreground_color . '; width: auto; height: auto;">' . $row->foreground_color . '</div>';
            })
            ->editColumn('background_color', function ($row) {
                return '<div class="btn rounded-pill" style="background-color:' . $row->background_color . '; width: auto; height: auto;">' . $row->background_color . '</div>';
            })
            ->editColumn('is_top', function ($row) {
                $checked = $row->is_top ? "checked" : '';
                $url = route('category.changeIsTop', ['id' => $row->id]);
                return '<div class="form-check form-switch">
                            <input class="form-check-input theme-choice change_is_top" data-url="' . $url . '" type="checkbox" id="light-mode-switch" ' . $checked . '>
                            <label class="form-check-label" for="light-mode-switch"></label>
                        </div>';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('name', 'LIKE', "%$search%");
                        $w->orWhere('status', 'LIKE', "%$search%");
                        $w->orWhere('order', 'LIKE', "%$search%");
                    });

                }
            })
            ->escapeColumns([])
            ->rawColumns(['foreground_color', 'background_color'])
            ->make(true);
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            $rules['name'] = 'required|string|max:255';
            $rules['foreground_color'] = 'required';
            $rules['background_color'] = 'required';
            if ($request->id) {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif';
            } else {
                $rules['icon'] = 'mimes:jpeg,jpg,png,gif|required';
            }

            $messsages = array(
                'name.required' => "The name field is required.",
                'order.required' => "The order field is required.",
            );

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('Category added successfully');
                if ($request->id) {
                    $model = Category::where('id', $request->id)->first();
                    if ($model) {
                        $category = $model;
                        $succssmsg = trans('Category updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $category = new Category;
                }
                $category->name = $request->name;
                $category->order = $request->order;
                $category->foreground_color = $request->foreground_color;
                $category->background_color = $request->background_color;
                $category->status = ($request->status == 1) ? 1 : 0;

                //icon upload code
                if ($request->hasFile('icon') && $request->icon) {
                    $dir = "category_icon/";

                    if ($category->icon) {
                        if (Storage::disk('local')->exists($dir . $category->icon)) {
                            Storage::delete($dir . $category->icon);
                        }
                    }
                    $extension = $request->file("icon")->getClientOriginalExtension();
                    $filename = uniqid() . "_" . time() . "." . $extension;
                    Storage::disk("local")->put($dir . $filename, \File::get($request->file("icon")));

                    $category->icon = $filename;
                }

                if ($category->save()) {
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
            $category = Category::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $category];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $category = Category::query()->where('id', $request->id)->first();

        if ($category->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }

    public function changeIsTop(Request $request)
    {
        $category = Category::query()->where('id', $request->id)->first();

        if ($category) {
            $category->is_top = !$category->is_top;
            $category->save();
            $result = ['status' => true, 'message' => trans('Updated successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Updating fail'];
        }
        return response()->json($result);

    }

    public function export()
    {

        return Excel::download(new CategoryNestedExport(), 'categories.xlsx');
    }
}
