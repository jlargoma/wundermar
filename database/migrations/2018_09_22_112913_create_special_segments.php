<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialSegments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('special_segments', function (Blueprint $table) {
		    $table->increments('id');
		    $table->date('start');
		    $table->date('finish');
		    $table->integer('minDays');
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
	    Schema::drop('special_segments');
    }
}
