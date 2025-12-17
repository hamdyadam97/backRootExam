<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\SubCategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Http\Resources\Category\CategoryResource;
use DB;

class CategoryController extends BaseController
{
    public function get_category(Request $request)
    {
        $category_data = Category::leftJoin('exams', function ($join) {
            $join->on('categories.id', '=', 'exams.cat_id');
            $join->whereNull('exams.deleted_at');
        })
            ->where('categories.status', 1)
            ->select('categories.*', DB::raw('COUNT(exams.id) as num_exams'));
        if (!empty($request->search)) {
            $category_data = $category_data->where('name', 'LIKE', '%' . $request->search . '%');
        }
        $category_data = $category_data->groupBy('categories.id')->get();

        $categories_data['categories'] = [];
        if ($category_data->count() == 0) {
            return $this->send_response('Category data', $categories_data);
        }
        foreach ($category_data as $category) {
            $categories_data['categories'][] = new CategoryResource($category);
        }
        return $this->send_response('Category data', $categories_data);
    }

    public function get_subcategory(Request $request)
    {

        $sub_categories = Subcategory::select('sub_categories.id', 'sub_categories.name', 'sub_categories.icon', 'sub_categories.order', 'sub_categories.background_color as sub_bg_color', 'sub_categories.foreground_color as sub_fore_color', 'sub_categories.status as sub_cat_status', 'categories.id as category_id', 'categories.name as category_name', 'exams.id as exam_id', 'exams.title as exam_title', 'exams.time as exam_time', DB::raw('COUNT(exam_questions.id) as no_of_questions'))
            ->leftJoin('categories', function ($join) {
                $join->on('categories.id', '=', 'sub_categories.cat_id');
                $join->whereNull('categories.deleted_at');
            })
            ->where('sub_categories.status', 1)
            ->leftJoin('exams', function ($join) {
                $join->on('exams.sub_cat_id', '=', 'sub_categories.id');
                $join->whereNull('exams.deleted_at');
            })
            ->leftJoin('exam_questions', function ($join) {
                $join->on('exam_questions.exam_id', '=', 'exams.id');
                $join->whereNull('exam_questions.deleted_at');
            });
        if (!empty($request->cat_id)) {
            $sub_categories = $sub_categories->where('categories.id', $request->cat_id);
        }
        if (!empty($request->search)) {
            $sub_categories = $sub_categories->where('sub_categories.name', 'LIKE', '%' . $request->search . '%');
        }
        $sub_categories = $sub_categories->groupBy('sub_categories.id', 'categories.id', 'exams.id')->get();


        $subcategory_list = [];
        if ($sub_categories->count() == 0) {
            return $this->send_response('Subcategory data', ['sub_categories' => $subcategory_list]);
        }
        foreach ($sub_categories as $sub_category) {
            // Check if subcategory has already been added to the list
            $subcategory_index = array_search($sub_category->id, array_column($subcategory_list, 'subcategory_id'));

            if ($subcategory_index === false) {
                // Add new subcategory to the list
                $subcategory_list[] = [
                    'subcategory_id' => $sub_category->id,
                    'category_name' => $sub_category->category_name,
                    'name' => $sub_category->name,
                    'icon' => !empty($sub_category->icon) ? asset('storage/subcategory_icon') . '/' . $sub_category->icon : null,
                    'order' => $sub_category->order,
                    'background_color' => $sub_category->sub_bg_color,
                    'foreground_color' => $sub_category->sub_fore_color,
                    'status_id' => $sub_category->sub_cat_status,
                    'status_label' => SubCategory::$status[$sub_category->sub_cat_status],
                    'exams' => []
                ];

                $subcategory_index = count($subcategory_list) - 1;
            }

            // Add exam to subcategory
            if (!is_null($sub_category->exam_id)) {
                $examData = [
                    'id' => $sub_category->exam_id,
                    'name' => $sub_category->exam_title,
                    'time' => $sub_category->exam_time,
                    'number_of_questions' => $sub_category->no_of_questions,
                ];

                $subcategory_list[$subcategory_index]['exams'][] = $examData;
            }

        }
        return $this->send_response('Subcategory data', ['sub_categories' => $subcategory_list]);
    }

    public function getCategory($id)
    {
        $category = Category::query()
            ->withCount(['questions' => function ($q) {
                $q->where('status', 1);
            }])
            ->find($id);
        if (!$category){
            return $this->send_response('Not Found' , []);
        }
        $data['category'] = new CategoryResource($category);
        return $this->api_response(true, __('translation.successfully'), $data);
    }
    public function getSubCategories($cat_id)
    {
        $sub_categories = SubCategory::query()->where('cat_id', $cat_id)->get();
        $data['sub_categories'] = SubCategoryResource::collection($sub_categories);
        $data['default_image'] = asset('default.png');
        return $this->api_response(true, __('translation.successfully'), $data);
    }
}
