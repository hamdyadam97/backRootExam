<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCategorySubcategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_subcategory', function (Blueprint $table) {
            // Add two columns to the "categories" table
            Schema::table('categories', function (Blueprint $table) {
                $table->string('foreground_color', 50)->after('order')->nullable();
                $table->string('background_color', 50)->after('order')->nullable();
            });

            // Add two columns to the "sub_categories" table
            Schema::table('sub_categories', function (Blueprint $table) {
                $table->string('foreground_color', 50)->after('order')->nullable();
                $table->string('background_color', 50)->after('order')->nullable();
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
        Schema::table('category_subcategory', function (Blueprint $table) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('foreground_color');
                $table->dropColumn('background_color');
            });
            Schema::table('sub_categories', function (Blueprint $table) {
                $table->dropColumn('foreground_color');
                $table->dropColumn('background_color');
            });
        });
    }
}
