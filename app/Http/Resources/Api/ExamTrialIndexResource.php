<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamTrialIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $categories = $this['categories']->pluck('name')->toArray();

        return [
            'id' => $this['id'],
            'title' => $this['title'],
            'question_count' => $this['question_count'],
            'mode' => $this->getMode(),
            'question_mode' => $this->getQuestionMode(),
            "categories" => count($categories) ? implode(', ', $categories) : null,
            'created_at' => Carbon::parse($this->created_at)->toDateString(),
        ];
    }
}
