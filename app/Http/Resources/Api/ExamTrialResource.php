<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamTrialResource extends JsonResource
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
            'title' => $this['title'],
            'question_count' => $this['question_count'],
            'mode' => $this->getMode(),
            'is_timed_mode' => (boolean)$this['is_timed_mode'],
            'is_tatur_mode' => $this['mode'] == "tatur",
            'question_mode' => $this->getQuestionMode(),
            "categories" => isset($this['categories']) ? CategoryResource::collection($this['categories']) : null,
            "sub_categories" => isset($this['subCategories']) ? SubCategoryResource::collection($this['subCategories']) : [],
            "sections" => isset($this['sections']) ? SectionResource::collection($this['sections']) : [],
            "topics" => isset($this['topics']) ? TopicResource::collection($this['topics']) : [],

            'total_questions' => $this['total_questions'],
            'correct_answers' => $this['correct_answers'],
            'wrong_answers' => $this['wrong_answers'],


            'created_at' => Carbon::parse($this->created_at)->toDateString(),
        ];
    }
}
