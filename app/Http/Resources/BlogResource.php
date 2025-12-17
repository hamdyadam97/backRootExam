<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => isset($this->description) ? Str::limit(strip_tags(str_replace('&nbsp;', ' ', $this->description)), 100) : '',
            'description' => $this->description,
            'category_id' => $this?->category_id,
            'category' => $this?->category?->name,
            'date' => Carbon::parse($this?->created_at)->format('Y-m-d H:i A'),
            'image' => isset($this->image) ? asset('storage/blogs_image/' . $this->image) : asset('default.png')
        ];
    }
}
