<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_trails', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('question_count')->default(0);
            $table->enum('mode', ['tatur', 'exam'])->default('tatur');
            $table->boolean('is_timed_mode')->default(false);
            $table->enum('question_mode', ['all', 'unused', 'used', 'correct', 'incorrect', 'omitted', 'marked'])->default('all');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_trails');
    }
};
