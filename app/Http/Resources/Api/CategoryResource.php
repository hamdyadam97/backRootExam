<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'icon' => $this['icon'] ? asset('storage/category_icon/' . $this['icon']) : null,
            'questions_count' => isset($this['questions_count']) ? $this['questions_count'] : $this['questions']->count(),
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
