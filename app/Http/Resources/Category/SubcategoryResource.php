<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SubCategory;
use App\Models\Category;

class SubcategoryResource extends JsonResource
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
        return [
            'category_name'=>$category->name,
            'subcategory_name'=>$this->name,
            'order'=>$this->order,
            'icon'=>!empty($this->icon)?asset('storage/subcategory_icon').'/'.$this->icon:null,
            'status_id'=>$this->status,
            'status_label'=>SubCategory::$status[$this->status]
        ];
    }
}
