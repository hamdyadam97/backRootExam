<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorRequest extends FormRequest
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
        $is_add = !$this->has('id');
        return [
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:5',
            'image' => $is_add  ? "required" : 'nullable'
        ];
    }
}
