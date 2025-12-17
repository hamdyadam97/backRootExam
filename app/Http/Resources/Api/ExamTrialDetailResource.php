<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamTrialDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'exam_id' => (int)$this['exam_trial_id'],
            'question_id' => (int)$this['question_id'],
            'answer_id' => isset($this['answer_id']) && $this['answer_id']? (int)$this['answer_id'] : null,
            'is_correct' => $this['is_correct'],
            'is_marked' => $this['is_marked'],
        ];
    }
}
