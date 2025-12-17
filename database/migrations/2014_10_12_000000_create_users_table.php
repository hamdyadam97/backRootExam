<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('mobile',20)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->text('thumb')->nullable();
            $table->string('device_id',255)->nullable();
            $table->integer('score')->nullable();
            $table->integer('role_type')->nullable()->comment('1=admin, 2= User');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token',255)->nullable();
            $table->string('password_token')->nullable();            
            $table->rememberToken();            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
