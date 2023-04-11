<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Promotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('promotions', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('channel')->nullable();
          $table->integer('site_id')->nullable();
          $table->string('room_group')->nullable();
          $table->string('discount',5)->nullable();
          $table->text('detail')->nullable();
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
        //
    }
}
