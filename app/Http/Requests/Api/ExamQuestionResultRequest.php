<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamQuestionResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $exam_id = $this->route()->parameter('exam_id');
        return [
            'result' => 'required|array',
            'result.*.exam_id' => 'required|in:' . $exam_id,
            'result.*.question_id' => 'required|exists:exam_trial_details,question_id,exam_trial_id,' . $exam_id,
            'result.*.answer_id' => 'nullable|exists:questions_answers,id',
         ];
    }

    public function attributes()
    {
        return [
            'result' => 'Result',
            'result.*.exam_id' => 'Exam',
            'result.*.question_id' => 'Question',
            'result.*.answer_id' => 'Answer',
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
