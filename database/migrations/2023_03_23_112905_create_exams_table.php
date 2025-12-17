<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->integer('cat_id')->nullable();
            $table->integer('sub_cat_id')->nullable();
            $table->string('title',255)->nullable();
            $table->text('description')->nullable();
            $table->string('icon',255)->nullable();
            $table->time('time')->nullable();
            $table->integer('type')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('score')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cat_id']);
            $table->index(['sub_cat_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
