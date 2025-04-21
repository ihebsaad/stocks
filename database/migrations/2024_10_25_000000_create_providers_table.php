<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50)->nullable();
            $table->string('lastname',50)->nullable();
            $table->string('company',50)->nullable();
            $table->string('email')->nullable();
            $table->string('phone',50)->nullable();
            $table->string('address')->nullable();
            $table->string('city',50)->nullable();
            $table->string('postal',50)->nullable();
            $table->string('country',50)->nullable();
            $table->string('email_contact')->nullable();
            $table->string('phone_contact',50)->nullable();

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
        Schema::dropIfExists('providers');
    }
}
