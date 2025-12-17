<?php

namespace App\Http\Resources\Package;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Packges;

class PackageResource extends JsonResource
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
            'id' => $this->id,
            'name'=>$this->name,
            'price'=>$this->price,
            'status_id'=>$this->status,
            'status_label'=>Packges::$status[$this->status],
            'icon'=>asset('storage/Package_icon').'/'.$this->icon,
            'period' =>$this->period,
            'number_of_questions'=>$this->number_of_questions,
            'no_of_exams'=>$this->no_of_exams,
            'no_of_trial'=>$this->no_of_trial,
        ];
    }
}
