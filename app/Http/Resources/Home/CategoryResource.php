<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Category;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'category_id'=>$this->id,
            'foreground_color'=>$this->foreground_color,
            'background_color'=>$this->background_color,
            'category_icon'=>!empty($this->icon)?asset('storage/category_icon').'/'.$this->icon:null,
            'category_title'=>$this->name,
            'total_category_exams'=>$this->total_exams
        ];
    }
}
