<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this['id'],
            "name" => $this['name'],
            "price" => $this['price'],
            "icon" => asset('storage/Package_icon') . '/' . $this['icon'],
            "period" => $this['period'],
            "category" => isset($this['category']) ? new CategoryResource($this['category']) : null,
            "sub_categories" => isset($this['subCategories']) ? SubCategoryResource::collection($this['subCategories']) : [],

//                "period": 30,
//                "number_of_questions": null,
//                "no_of_exams": 0,
//                "no_of_trial": 0

        ];
    }
}
