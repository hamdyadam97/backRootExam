<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
//                'exam_id'=>'required',
            'text_question' => 'required|string|',
//            'question_type' => 'required',
// 'notes'=>'required',
            'answer_type' => 'required',
            'answer_option.*' => 'required',
//            'show_answer_explanation' => 'required',
            'correct_answer_editor' => 'required|array|min:4',
            'correct_answer_editor.*' => 'required',
            'correct_answer_editor.4' => 'nullable',
//            'correct_answer_editor.4' => 'nullable',
//            'section_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required|exists:sub_categories,id,cat_id,' . $this->category_id,
            'sub_subcategory_id' => 'nullable|exists:sub_sub_categories,id,sub_cat_id,' . $this->sub_category_id,
            // 'sub_subcategory_id' => 'required',
            'section_id' => 'nullable|exists:exam_section,id,category_id,' . $this->category_id ,
            'questions_topic_id' => 'nullable|exists:questions_topics,id,category_id,' . $this->category_id ,
            'video_link' => 'nullable|url',

        ];
    }

    public function messages()
    {
        return [
            'exam_id.required' => 'The exam field is required.',
            'text_question.required' => "The text field is required.",
            'answer_option.*.required' => "This correct answer field is required.",
            'correct_answer_editor.*.required' => "This field is required.",
            'section_id.required' => "Exam section field is required.",

        ];
    }

    public function attributes(){
        return [
            'sub_subcategory_id' => 'sub subcategory'
        ];
    }
}
