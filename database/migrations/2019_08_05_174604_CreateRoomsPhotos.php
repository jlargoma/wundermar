<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('rooms_photos', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('room_id')->unsigned()->nullable();
          $table->foreign('room_id')->references('id')->on('rooms');
          $table->string('file_rute');
          $table->string('file_name');
          $table->string('status');
          $table->string('gallery_key')->nullable();
          $table->integer('position')->nullable();
          $table->boolean('main')->nullable();
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
      Schema::drop('rooms_photos');
    }
}
