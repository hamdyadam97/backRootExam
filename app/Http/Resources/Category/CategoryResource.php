<?php

namespace App\Http\Resources\Category;

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
            'id'=>$this->id,
            'name'=>$this->name,
            'icon'=>asset('storage/category_icon').'/'.$this->icon,
            'order'=>$this->order,
            'foreground_color'=>$this->foreground_color,
            'background_color'=>$this->background_color,
            'status_id'=>$this->status,
            'status_label'=>Category::$status[$this->status],
            'number_of_exams'=>$this->num_exams
        ];
    }
}
