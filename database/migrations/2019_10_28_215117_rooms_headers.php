<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoomsHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('rooms_headers', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('room_id')->nullable();
          $table->unsignedBigInteger('room_type_id')->nullable();
          $table->string('img_desktop')->nullable();
          $table->string('img_mobile')->nullable();
          $table->string('url')->nullable();
          
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
