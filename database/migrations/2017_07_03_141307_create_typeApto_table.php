<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeAptoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //
        //
        Schema::create('typeapto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });


        Schema::table('rooms', function ($table) {            
            $table->foreign('typeApto')->references('id')->on('typeapto');
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
