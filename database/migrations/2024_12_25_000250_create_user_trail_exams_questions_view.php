<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW user_trail_exams_questions AS
             SELECT
                et.user_id,
                et.id,
                etd.question_id,
                etd.is_correct,
                qs.category_id,
                categories.name AS category_name,
                qs.sub_category_id,
                sub_categories.name AS sub_category_name,
                qs.sub_subcategory_id,
                sub_sub_categories.name AS sub_sub_category_name,
                question_topics.topic_id,
                questions_topics.topic AS topic_name,
                exam_sections.section_id,
                exam_section.name AS section_name
            FROM
                exam_trails AS et
            LEFT JOIN exam_trial_details AS etd ON et.id = etd.exam_trial_id
            LEFT JOIN questions AS qs ON etd.question_id = qs.id
            LEFT JOIN categories ON categories.id = qs.category_id
            LEFT JOIN sub_categories ON sub_categories.id = qs.sub_category_id
            LEFT JOIN sub_sub_categories ON sub_sub_categories.id = qs.sub_subcategory_id
            LEFT JOIN question_topics ON question_topics.question_id = qs.id
            LEFT JOIN exam_sections ON exam_sections.question_id = qs.id
            LEFT JOIN questions_topics ON questions_topics.id = question_topics.topic_id
            LEFT JOIN exam_section  ON exam_section.id = exam_sections.section_id;
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW user_trail_exams_questions");
    }
};
