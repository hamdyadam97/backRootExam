<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class SubCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (isset($this['questions_count'])){
            $questions_count = $this['questions_count'];
        }else{
            $questions_count = $this['questions']->where('status', 1)->count();
        }

        return [
            'id' => $this['id'],
            'category_id' => $this['cat_id'],
            'name' => $this['name'],
            'icon' => $this['icon'] ? asset('storage/subcategory_icon/'.$this['icon']) : null,

            'questions_count' => $questions_count,
            'background_color' => $this['background_color'],
            'foreground_color' => $this['foreground_color'],
        ];
    }

    public static function toPackage(Collection|array $collection)
    {
        return collect($collection)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        });
    }
}
