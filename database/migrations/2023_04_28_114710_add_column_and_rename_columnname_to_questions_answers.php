<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAndRenameColumnnameToQuestionsAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions_answers', function (Blueprint $table) {
            $table->integer('exam_id')->after('id')->nullable();
            $table->renameColumn('answer', 'answer_option');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions_answers', function (Blueprint $table) {
            $table->dropColumn('exam_id');
        });
    }
}
