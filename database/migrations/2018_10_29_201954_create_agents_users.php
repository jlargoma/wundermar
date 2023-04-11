<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('agents_rooms', function (Blueprint $table) {
		    $table->increments('id');
		    $table->integer('user_id')->unsigned();
		    $table->foreign('user_id')->references('id')->on('users');
		    $table->integer('room_id')->unsigned();
		    $table->foreign('room_id')->references('id')->on('rooms');
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
	    Schema::drop('agents_rooms');
    }
}
