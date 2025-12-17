<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamQuestionSingleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        $exam_id = $this->route()->parameter('exam_id');
        return [
            'exam_id' => 'required|in:' . $exam_id,
            'question_id' => 'required|exists:exam_trial_details,question_id,exam_trial_id,' . $exam_id,
            'answer_id' => 'nullable|exists:questions_answers,id',
        ];
    }

    public function attributes()
    {
        return [
            'exam_id' => 'Exam',
            'question_id' => 'Question',
            'answer_id' => 'Answer',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation Errors',
            'errors' => $validator->errors()
        ], 422));
    }

}
