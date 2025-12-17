<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopicRequest;
use App\Models\Category;
use App\Models\QuestionsTopic;
use Illuminate\Http\Request;

class TopicController extends Controller
{

    public function index()
    {
        $data['categories'] = Category::query()->active()->get();
        return view('topics.index', $data);
    }

    public function get(Request $request)
    {

        $data = QuestionsTopic::query()->with(['category'])->filter()->latest();

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('category_name', function ($row) {
                return $row?->category?->name ?? "-";
            })
            ->make();
    }

    public function addupdate(TopicRequest $request)
    {
        if ($request->ajax()) {
            $succssmsg = $request->id ? trans('Topic updated successfully') : trans('Topic added successfully');
            $data = $request->all();

            QuestionsTopic::query()->updateOrCreate([
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
            $item = QuestionsTopic::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $item];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $item = QuestionsTopic::query()->where('id', $request->id)->first();

        if ($item->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}
