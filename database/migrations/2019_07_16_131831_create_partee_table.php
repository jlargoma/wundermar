<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParteeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('book_partees', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('book_id')->unsigned();
          $table->foreign('book_id')->references('id')->on('book');
          $table->integer('partee_id');
          $table->string('link');
          $table->string('status');
          $table->integer('guestNumber')->nullable();
          $table->boolean('sentSMS')->nullable();
          $table->text('log_data');
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
      Schema::drop('book_partees');
    }
}
