<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exams;
use App\Models\Questions;
use App\Models\Category;
use App\Models\SubCategory;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\SubcategoryResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $category = Category::find($this->cat_id);
        $subcategory = SubCategory::find($this->sub_cat_id);
        $category_data = new CategoryResource($category);
        $subcategory_data = new SubcategoryResource($subcategory);
        return [
            'id' => $this->id,
            'category'=>$category_data,
            'subcategory'=>$subcategory_data,
            'title'=>$this->title,
            'description'=>$this->description,
            'icon'=>!empty($this->icon)?asset('storage/exam_icon').'/'.$this->icon:null,
            'time'=>$this->time,
            'exam_type_id'=>$this->type,
            'exam_type_label'=>Exams::$exam_type[$this->type],
            'status_id'=>$this->status,
            'status_label'=>Exams::$status[$this->status],
            'score'=>$this->score,
        ];
    }
}
