<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamSectionRequest;
use App\Models\Category;
use App\Models\ExamSection;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ExamSectionController extends Controller
{

    public function index()
    {
        $data['categories'] = Category::query()->active()->get();
//        $data['sub_categories'] = SubCategory::query()->active()->get();

        return view('exam_sections.index', $data);
    }

    public function get(Request $request)
    {

        $data = ExamSection::query()->with(['category'])->filter()->latest();

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('category_name', function ($row) {
                return $row?->category?->name ?? "-";
            })
//            ->addColumn('sub_category_name', function ($row) {
//                return $row?->subcategory?->name ?? "-";
//            })
            ->make(true);
    }

    public function addupdate(ExamSectionRequest $request)
    {
        if ($request->ajax()) {
            $succssmsg = $request->id ? trans('Exam section updated successfully') : trans('Exam section added successfully');
            $data = $request->all();

            ExamSection::query()->updateOrCreate([
                'id' => $request->id,
            ], $data);

            $result = ['status' => true, 'message' => $succssmsg, 'data' => []];

        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
        }
        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $item = ExamSection::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $item];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $item = ExamSection::query()->where('id', $request->id)->first();

        if ($item->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}
