<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\InstructorResource;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Instructor;
use DB;

class BlogController extends BaseController
{

    public function index()
    {

        $blogs = Blog::query()->with('category')->where('status', 1)->get();
        $categories = Blog::query()->pluck('category_id')->toArray();

        $categories = Category::query()->where('status', 1)
            ->whereIn('id', $categories)->get();
        $data['categories'] = CategoryResource::collection($categories);


        $data['blogs'] = BlogResource::collection($blogs);
        $data['default_image'] = asset('default.png');
        return $this->api_response(true, 'Done Successfully', $data);
    }

    public function details($id)
    {
        $blog = Blog::query()->with('category')->where('status', 1)->find($id);
        if (!$blog) {
            return $this->api_response(false, 'Blog Not Found', [], 404);
        }
        $data['blog'] = new BlogResource($blog);
        $data['default_image'] = asset('default.png');

        return $this->api_response(true, 'Done Successfully', $data);
    }

}
