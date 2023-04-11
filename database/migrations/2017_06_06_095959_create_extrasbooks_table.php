<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtrasbooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extrasbooks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('extra_id')->unsigned();
            $table->foreign('extra_id')->references('id')->on('extras');
            $table->integer('book_id')->unsigned();
            $table->foreign('book_id')->references('id')->on('book');
            $table->timestamps();
        });

        Schema::table('extrasbooks', function ($table) {
            
            
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
