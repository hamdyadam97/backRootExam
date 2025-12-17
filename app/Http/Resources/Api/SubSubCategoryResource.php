<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubSubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (isset($this['questions_count'])) {
            $questions_count = $this['questions_count'];
        } else {
            $questions_count = $this['questions']->where('status', 1)->count();
        }

        return [
            'id' => $this['id'],
            'sub_cat_id' => $this['sub_cat_id'],
            'name' => $this['name'],
            'icon' => $this['icon'],
            'questions_count' => $questions_count,
            'background_color' => $this['background_color'],
            'foreground_color' => $this['foreground_color'],
        ];
    }
}
