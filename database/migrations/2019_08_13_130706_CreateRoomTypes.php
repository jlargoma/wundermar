<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('rooms_types', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name');
          $table->string('title');
          $table->text('description');
          $table->string('status');
          $table->string('gallery_key')->nullable();
          $table->integer('position')->nullable();
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
      Schema::drop('rooms_types');
    }
}
