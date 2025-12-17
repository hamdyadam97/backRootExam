<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_categories_exam_trails', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id');
            $table->unsignedBigInteger('exam_trail_id');
            $table->primary(['exam_trail_id' , 'sub_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories_exam_trails');
    }
};
