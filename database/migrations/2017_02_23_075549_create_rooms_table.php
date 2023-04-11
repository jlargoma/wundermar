<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('nameRoom');
            $table->integer('owned')->unsigned();
            $table->integer('sizeApto')->unsigned();
            $table->integer('typeApto')->unsigned();
            $table->integer('minOcu');
            $table->integer('maxOcu');
            $table->integer('luxury');
            $table->integer('profit_percent');
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
