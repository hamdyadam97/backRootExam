<?php

namespace App\Exports;

use App\Models\Questions;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

//ShouldAutoSize,
class QuestionExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
{

    public function collection()
    {
        return Questions::query()
            ->with(['questions_answers', 'exam_sections', 'question_topic'])
            ->filter()->orderByDesc('questions.id')
            ->get();
    }

    public function map($row): array
    {
        $exam_sections = $row->exam_sections->pluck('section_id')->toArray();
        $exam_topic = $row->question_topic->pluck('topic_id')->toArray();
        $result = [
            $row['id'],
            $row['category_id'],
            $row['sub_category_id'],
            $row['sub_subcategory_id'],
            implode(',', $exam_sections),
            $row['text_question'],
            $row['notes'],
            $row['answer_type'],
            $row['hint'],
            $row['video_link'],
            implode(',', $exam_topic),
        ];

        for ($i = 0; $i < 5; $i++) {
            $result[] = $row->questions_answers[$i]?->answer_option;
        }

        $result[] = $row->correct_answer_id;

        return $result;
    }


    public function headings(): array
    {
        return [
            'id',
            'category_id',
            'sub_category_id',
            'sub_sub_category_id',
            'section_ids',

            'text_question',
            'notes',
            'answer_type',
            'hint',
            'video_link',
            'questions_topic_id',


            'correct_answer1',
            'correct_answer2',
            'correct_answer3',
            'correct_answer4',
            'correct_answer5',

            'correct_answer_id',

        ];
    }
}
