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
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedBigInteger('sub_category_id')->change();
            $table->unsignedBigInteger('sub_subcategory_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->change();
            $table->unsignedInteger('sub_category_id')->change();
            $table->unsignedInteger('sub_subcategory_id')->change();
        });
    }
};
