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
        Schema::table('exam_section', function (Blueprint $table) {
            $table->integer('category_id')->after('name')->nullable();
            $table->integer('sub_category_id')->after('category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_section', function (Blueprint $table) {
            $table->dropColumn(['category_id', 'sub_category_id', 'created_at', 'updated_at']);

        });
    }
};
