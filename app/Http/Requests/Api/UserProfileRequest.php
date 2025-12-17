<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserProfileRequest extends FormRequest
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
        $user_id = auth()->id();

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user_id,

            'mobile_country_code' => 'required',
            'dial_code' => 'required',
            'mobile_number' => 'required|numeric',
            'mobile' => 'required|numeric|unique:users,mobile,' . $user_id,

        ];
    }

    protected function prepareForValidation()
    {
        $dialCode = ltrim(stringNumberToInteger(trim($this->get('dial_code'))), '+');
        $mobileNumber = ltrim(stringNumberToInteger(trim($this->request->get('mobile_number'))), '0');

        $this->merge([
            'mobile_number' => $mobileNumber,
            'dial_code' => $dialCode,
            'mobile' => $dialCode . $mobileNumber
        ]);

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
