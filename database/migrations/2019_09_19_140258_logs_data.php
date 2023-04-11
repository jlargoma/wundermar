<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LogsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('logs_data', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('key')->nullable();
          $table->string('data')->nullable();
          $table->text('long_info')->nullable();
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
      Schema::drop('logs_data');
    }
}
