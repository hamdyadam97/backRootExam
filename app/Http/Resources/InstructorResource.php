<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class
InstructorResource extends JsonResource
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
            'specialization'=>$this->specialization,
            'rate'=>$this->rate,
            'image' => isset($this->image) ? asset('storage/instructor_image/'.$this->image) : asset('default.png')
        ];
    }
}
