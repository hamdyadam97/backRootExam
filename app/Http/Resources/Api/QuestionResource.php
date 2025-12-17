<?php

namespace App\Http\Resources\Api;

use App\Models\Questions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'text_question' => $this['text_question'],
            'answer_type' => $this['answer_type'],
            'answer_type_str' => Questions::$answer_type[$this['answer_type']],
            'correct_answer_id' => (int)$this['correct_answer_id'],

            'show_hint' => (boolean)$this['show_hint'],
            'hint' => $this['hint'],

            'show_video' => (boolean)$this['show_video'],
            'video_link' => $this['video_link'],

            'question_has_image' => (boolean)$this['question_has_image'],
            'question_image' => ((boolean)$this['question_has_image']) && $this['question_image'] ? asset('storage/question_images/' . $this['question_image']) : null,

            'answer_has_image' => (boolean)$this['answer_has_image'],
            'answer_image' => ((boolean)$this['answer_has_image']) && $this['answer_image'] ? asset('storage/answer_images/' . $this['answer_image']) : null,

            'show_answer' => (boolean)$this['show_answer'],

            'is_show_answer_explanation' => (boolean)$this['show_answer_explanation'],
            'notes' => $this['notes'],

            'answers' => QuestionAnswerResource::collection($this['questions_answers']),
//            'mode' => $this->getMode(),
//            'question_mode' => $this->getQuestionMode(),
//            "categories" => isset($this['categories']) ? CategoryResource::collection($this['categories']) : null,
//            "sub_categories" => isset($this['subCategories']) ? SubCategoryResource::collection($this['subCategories']) : [],
//            "sections" => isset($this['sections']) ? SectionResource::collection($this['sections']) : [],
//            "topics" => isset($this['topics']) ? TopicResource::collection($this['topics']) : [],
//
//            'created_at' => Carbon::parse($this->created_at)->toDateString(),
        ];
    }
}
