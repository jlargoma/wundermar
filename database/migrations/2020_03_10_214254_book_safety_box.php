<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookSafetyBox extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('book_safety_boxs', function (Blueprint $table) {
        
        $table->bigIncrements('id');
        $table->integer('book_id')->nullable();
        $table->string('key')->nullable();
        $table->string('psw')->nullable();
        $table->text('log')->nullable();
        $table->boolean('deleted')->nullable();
        
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
