<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Category;

class NewExamsResource extends JsonResource
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
            'category_id'=>$this->category_id,
            'exam_id'=>$this->exam_id,
            'category_title'=>$this->category_title,
            'exam_title'=>$this->exam_title,
            'total_exam_questions'=>$this->total_questions,
            'exam_time'=>$this->exam_time,
            'background_color'=>$this->background_color,
            'foreground_color'=>$this->foreground_color,
        ];
    }
}
