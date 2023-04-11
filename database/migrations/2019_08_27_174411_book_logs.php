<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('book_logs', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('book_id');
          $table->foreign('book_id')->references('id')->on('book');
          $table->integer('room_id')->nullable();
          $table->integer('user_id')->nullable();
          $table->string('cli_email')->nullable();
          $table->string('action')->nullable();
          $table->string('subject')->nullable();
          $table->text('content')->nullable();
          $table->string('status')->default(0);
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
       Schema::drop('book_logs');
    }
}
