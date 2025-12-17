<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToExamsAndUserExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams_and_user_exams', function (Blueprint $table) {
            Schema::table('exams', function (Blueprint $table) {
                $table->integer('order')->after('status')->nullable();
                $table->text('hint')->after('order')->nullable();
                $table->tinyInteger('show_hint')->default(0)->after('hint')->comment('0=>no,1=>yes')->nullable();
                $table->tinyInteger('show_answer')->default(0)->after('show_hint')->comment('0=>no,1=>yes')->nullable();
                $table->string('video_link')->after('show_answer')->nullable();
            });
            Schema::table('user_exams', function (Blueprint $table) {
                $table->date('start_date')->after('score')->nullable();
                $table->date('end_date')->after('start_date')->nullable();
                $table->tinyInteger('status')->default(0)->after('end_date')->comment('0=>new,1=>in_preogress,2=>submitted')->nullable();
                $table->dropColumn('user_exams_date');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams_and_user_exams', function (Blueprint $table) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn('order');
                $table->dropColumn('hint');
                $table->dropColumn('show_hint');
                $table->dropColumn('show_answer');
                $table->dropColumn('video_link');
            });
            Schema::table('user_exams', function (Blueprint $table) {
                $table->dropColumn('start_date');
                $table->dropColumn('end_date');
            });
        });
    }
}
