<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopicRequest extends FormRequest
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
        return [
            'category_id' => 'required|exists:categories,id',
            'topic' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => "Please select Category",
            'sub_category_id.required' => "Please select Sub Category",
//            'category_id.exists' => "Category must be exists in categories",
//            'sub_category_id.exists' => "Category must be exists in sub categories",

        ];
    }
}
