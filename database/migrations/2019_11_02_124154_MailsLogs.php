<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MailsLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('mails_logs', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('uid')->nullable();
          $table->string('subject')->nullable();
          $table->string('address')->nullable();
          $table->string('from_user')->nullable();
          $table->string('time_msg')->nullable();
          $table->text('msg')->nullable();
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
