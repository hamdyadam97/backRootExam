<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExamTrialRequest extends FormRequest
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
        $user = request()->user();
        $categories = $user->categories()->pluck('id')->toArray();
        $sub_categories = collect($user->subcategories())->pluck('id')->toArray();
        $sub_sub_categories = collect($user->subSubcategories())->pluck('id')->toArray();

        return [
            'title' => 'required|string|max:255',
            'question_count' => 'required|numeric|min:1|max:100',
            'exam_mode' => 'required|in:tatur,exam',
            'is_timed_mode' => 'required|boolean',
            'question_mode' => 'required',
            'categories' => 'required|array',
            'categories.*' => 'required|in:' . implode(',', $categories),
            'sub_categories' => 'nullable|array',
            'sub_categories.*' => 'required|in:' . implode(',', $sub_categories),
            'sub_sub_categories' => 'nullable|array',
            'sub_sub_categories.*' => 'required|in:' . implode(',', $sub_sub_categories),
            'sections' => 'nullable|array',
            'sections.*' => 'required|exists:exam_section,id',
            'topics' => 'nullable|array',
            'topics.*' => 'required|exists:questions_topics,id',
        ];
    }

    public function attributes()
    {
        return [
            'exam_mode' => 'Test Mode',
            'is_timed_mode' => 'Timed Mode',
            'question_mode' => 'Question Mode',
            'categories' => 'Category',
            'categories.*' => 'Category',
            'sub_categories' => 'Sub Category',
            'sub_categories.*' => 'Sub Category',
            'sections' => 'Sections',
            'sections.*' => 'Sections',
            'topics' => 'Topics',
            'topics.*' => 'Topics',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'    => false,
            'message' => 'Validation Errors',
            'errors' => $validator->errors()
        ], 422));
     }

}
