<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{

    public function index()
    {
        $data['categories'] = Category::query()->get();

        return view('blogs.index', $data);
    }


    public function get(Request $request)
    {

        $data = Blog::query()->orderByDesc('id')->filter();
        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    return "<h5><span class='badge bg-primary'>Active</span></h5>";
                } else {
                    return "<h5><span class='badge bg-danger'>Deactive</span></h5>";
                }
            })
            ->editColumn('image', function ($row) {
                $url = asset('storage/blogs_image/' . $row->image);
                return '<img src="' . $url . '" width="50" />';
            })
            ->editColumn('category', function ($row) {
                return $row?->category?->name;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        $data['categories'] = Category::query()->get();

        return view('blogs.create', $data);
    }

    public function store(BlogRequest $request)
    {
        $data = $request->all();

        if (!empty($request->image)) {
            $dir = "blogs_image/";
//            if (@$model->image) {
//                Storage::delete($dir . $model->image);
//            }

            $extension = $request
                ->file("image")
                ->getClientOriginalExtension();
            $filename = uniqid() . "_" . time() . "." . $extension;
            Storage::disk("local")->put($dir . $filename, File::get($request->file("image")));
            $data['image'] = $filename;
        }

        Blog::query()->create($data);
        $succssmsg = trans('Blog added successfully');
        $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
        return response()->json($result);
    }


    public function edit($id)
    {
        $data['categories'] = Category::query()->get();
        $data['item'] = Blog::query()->findOrFail($id);
        return view('blogs.create', $data);
    }

    public function update($id, BlogRequest $request)
    {
        $data = $request->all();
        $blog = Blog::query()->find($id);
        if (!$blog){
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
            return response()->json($result);
        }
        if (!empty($request->image)) {
            $dir = "blogs_image/";
            if (@$blog->image) {
                Storage::delete($dir . $blog->image);
            }

            $extension = $request
                ->file("image")
                ->getClientOriginalExtension();
            $filename = uniqid() . "_" . time() . "." . $extension;
            Storage::disk("local")->put($dir . $filename, File::get($request->file("image")));
            $data['image'] = $filename;
        }

        $blog->update($data);
        $succssmsg = trans('Blog added successfully');
        $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
        return response()->json($result);
    }


    public function delete(Request $request)
    {
        $item = Blog::query()->find($request->id);
        if ($item->delete()) {
            $result = ['status' => true, 'message' => trans('Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }
}
