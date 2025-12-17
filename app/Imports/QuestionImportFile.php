<?php

namespace App\Imports;

use App\Models\ExamSections;
use App\Models\Questionanswers;
use App\Models\Questions;
use App\Models\QuestionTopic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionImportFile implements ToModel, WithHeadingRow, WithValidation
{

    public function model(array $row)
    {
//        dd($row);
        DB::beginTransaction();

        try {
            $importQuestion = Questions::query()->create([
                'category_id' => $row['category_id'],
                'sub_category_id' => $row['sub_category_id'],
                'sub_subcategory_id' => $row['sub_sub_category_id'],
                'text_question' => $row['text_question'],
                'notes' => $row['notes'],
                'question_type' => 2,
                'answer_type' => $row['answer_type'],
                'hint' => @$row['hint'],
                'show_hint' => 1,
                'show_answer' => 1,
                'show_answer_explanation' => 1,
                'show_video' => 1,
                'time_minutes' => 1,
                'video_link' => $row['video_link'],
            ]);


            if (isset($row['section_ids']) && is_array(explode(',', $row['section_ids']))) {
                $sections = explode(',', $row['section_ids']);
                foreach ($sections as $section_id) {
                    if (!$section_id) {
                        continue;
                    }
                    ExamSections::query()->create([
                        'question_id' => $importQuestion->id,
                        'section_id' => $section_id,
                    ]);
                }
            }


            if (isset($row['questions_topic_id']) && is_array(explode(',', $row['questions_topic_id']))) {
                $topics = explode(',', $row['questions_topic_id']);
                foreach ($topics as $questions_topic_id) {
                    QuestionTopic::query()->create([
                        'question_id' => $importQuestion->id,
                        'topic_id' => $questions_topic_id,
                    ]);
                }
            }

            $correct_answer_numbers = explode(',', $row['correct_answer_numbers']);
            $correct_answer_ids = [];
            for ($i = 1; $i <= 5; $i++) {
                $q_answer = Questionanswers::query()->create([
                    'exam_id' => 1,
                    'question_id' => $importQuestion->id,
                    'answer_option' => @$row['correct_answer' . $i],
                ]);
                if (in_array($i, $correct_answer_numbers)) {
                    $correct_answer_ids[] = $q_answer->id;
                }
            }

            $importQuestion->correct_answer_id = implode(",", $correct_answer_ids);
            $importQuestion->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
        }

    }

    public function rules(): array
    {
        return [
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'sub_sub_category_id' => 'required',
            'text_question' => 'required',
            'notes' => 'nullable',
            'answer_type' => 'required',
            'hint' => 'nullable',
            'video_link' => 'nullable',
            'correct_answer_numbers' => 'required',
        ];
    }
}
