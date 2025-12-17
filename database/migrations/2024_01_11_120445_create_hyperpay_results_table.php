<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHyperpayResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hyperpay_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('item_id');
            $table->string('type', 10);
            $table->string('payment_id', 100);
            $table->string('payment_brand', 20);
            $table->string('transaction_id', 50);
            $table->decimal('amount', 19, 2);
            $table->longText('result');
            $table->longText('result_details');
            $table->longText('card');
            $table->longText('customer');
            $table->longText('custom_parameters');
            $table->integer('is_success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hyperpay_results');
    }
}
