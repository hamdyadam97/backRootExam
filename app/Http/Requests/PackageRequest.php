<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'icon' => 'mimes:jpeg,jpg,png,gif',
//            'icon' => !isset($this->id) ? 'required|mimes:jpeg,jpg,png,gif' : 'mimes:jpeg,jpg,png,gif',
            'price' => 'required|numeric',
            'period' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|array',
            'sub_category_id.*' => 'required|exists:sub_categories,id,cat_id,' . $this->category_id,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "The name field is required.",
            'price.required' => "The price field is required.",
            'price.numeric' => "Please enter valid number",
//            'number_of_questions.required' => "The number of questions field is required.",
//            'number_of_questions.numeric' => "Please enter valid number",
            'period.required' => "The period field is required.",
        ];
    }
}
