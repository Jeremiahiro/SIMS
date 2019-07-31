<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('full_name');
            $table->string('email');
            $table->string('password');
            $table->string('token')->nullable();
            
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('dob')->nullable();
            $table->string('pob')->nullable();
            $table->string('lga')->nullable();
            $table->string('state')->nullable();
            $table->string('level')->nullable();
            $table->string('avater')->nullable();
            $table->string('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('nationality')->nullable();
            $table->string('marital_status')->nullable();
            
            $table->string('email_verified_at')->nullable();            
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
        Schema::dropIfExists('users');
    }
}
