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
        Schema::create('discounts_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('marketer');
            $table->enum('type',[1,2])->comment("1-percentage, 2-amount");
            $table->double('percentage')->nullable();
            $table->double('amount')->nullable();
            $table->double('quantity');
            $table->date('from_date');
            $table->date('to_date');
            $table->tinyInteger('status')->comment('1-active, 2-in active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts_codes');
    }
};
