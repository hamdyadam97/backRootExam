<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackageRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Exams;
use App\Models\Packges;
use App\Models\PackageExam;
use Auth;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Facades\Storage;

class PackgesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['exams'] = Exams::all();
        $data['categories'] = Category::query()->with('subCategories')->get();
        return view('package.index', $data);
    }

    public function get(Request $request)
    {
        $status = Packges::$status;
        $data = Packges::query()->with(['category','subCategories'])->orderByDesc('id')->filter();

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('category', function ($row) {
                return $row?->category?->name;
            })
            ->addColumn('sub_category', function ($row) {
                return implode(" | " , $row?->subCategories?->pluck('name')->toArray());
            })
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->escapeColumns([])
            ->make();
        die();
    }

    public function addupdate(PackageRequest $request)
    {
        if ($request->ajax()) {
            $succssmsg = $request->id ? trans('Package updated successfully') : trans('Package added successfully');
            $package = null;

            if ($request->id) {
                $package = Packges::query()->find($request->id);
            }

            $data = $request->only(Packges::FILLABLE);

            //icon upload code
            if ($request->hasFile('icon') && $request->icon) {
                $dir = "Package_icon/";

                if (isset($package->icon)) {
                    if (Storage::disk('local')->exists($dir . $package->icon)) {
                        Storage::delete($dir . $package->icon);
                    }
                }
                $extension = $request->file("icon")->getClientOriginalExtension();
                $filename = uniqid() . "_" . time() . "." . $extension;
                Storage::disk("local")->put($dir . $filename, \File::get($request->file("icon")));

                $data['icon'] = $filename;
            }

            $package = Packges::query()->updateOrCreate(['id' => $request->id], $data);
            $package->subCategories()->sync($request['sub_category_id']);
            $result = ['status' => true, 'message' => $succssmsg, 'data' => []];

            return response()->json($result);
        }
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $Package = Packges::query()->with(['subCategories'])->find($request->id);
            $sub_categories = $Package->subCategories->pluck('id')->toArray();
            $Package->sub_category_ids = $sub_categories;
            $result = ['status' => true, 'message' => '', 'data' => $Package];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $Package = Packges::where('id', $request->id)->first();

        if ($Package->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}


