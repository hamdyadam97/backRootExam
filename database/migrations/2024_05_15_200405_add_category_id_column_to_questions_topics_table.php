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
        Schema::table('questions_topics', function (Blueprint $table) {
            $table->integer('category_id')->after('topic')->nullable();
            $table->integer('sub_category_id')->after('category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions_topics', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'sub_category_id']);
        });
    }
};
