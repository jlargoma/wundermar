<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Solicitudes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->integer('phone');
            $table->date('start');
            $table->date('finish');
            $table->timestamps();
        });

        Schema::create('solicitudes_productos', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('id_solicitud')->unsigned();
            $table->foreign('id_solicitud')->references('id')->on('solicitudes');
            $table->string('name');
            $table->integer('price')->default(0);

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
