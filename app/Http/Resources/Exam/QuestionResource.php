<?php

namespace App\Http\Resources\Exam;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Questions;
use App\Models\ExamSection;
use App\Models\Questionanswers;
use DB;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $correct_answer_options = Questionanswers::select('answer_option','id')->where('exam_id',$this->exam_id)->where('question_id',$this->id)->get();
        $answer_options = [];
        if(!empty($correct_answer_options)){
            foreach ($correct_answer_options as $correct_answer_option) {
                $answer_options[] = ["option_id"=>$correct_answer_option->id,"option_name"=>$correct_answer_option->answer_option];
            }
        }
        return [
            'id' => $this->id,
            'text_question'=>$this->text_question,
            'notes'=>$this->notes,
            // 'status_id'=>$this->status,
            // 'status_label'=>!empty($this->status)?Questions::$status[$this->status]:null,
            'question_type_id'=>$this->question_type,
            'question_type_label'=>!empty($this->question_type)?Questions::$questionType[$this->question_type]:null,
            'answer_type_id'=>$this->answer_type,
            'answer_type_label'=>(!empty($this->answer_type))?Questions::$answer_type[$this->answer_type]:null,
            'answer_options'=>(!empty($answer_options))?$answer_options:[],
            'correct_answer_ids'=>(!empty($this->correct_answer_id))?$this->correct_answer_id:"",
            'user_selected_answer'=>(!empty($this->user_selected_answer)) ? $this->user_selected_answer : "",
            'hint'=>$this->hint,
            'show_hint'=>$this->show_hint,
            'show_answer'=>$this->show_answer,
            'video_link'=>$this->video_link,
            'show_video'=>$this->show_video,
            'show_answer_explanation'=>$this->show_answer_explanation,
            'time_minutes'=>$this->time_minutes,
            'question_has_image'=>$this->question_has_image,
            'answer_has_image'=>$this->answer_has_image,
            'question_image'=>($this->question_image) ? asset('storage/question_images').'/'.$this->question_image : "",
            'answer_image'=>($this->answer_image) ? asset('storage/answer_images').'/'.$this->answer_image : "",
            // 'section'=>($this->section_id) ? ExamSection::find($this->section_id) : "",
            'is_correct'=>$this->correct_answer_id==$this->user_selected_answer?1:0,
        ];

    }
}
