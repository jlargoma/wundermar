<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RateCheckerSnaphot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('rate_checker_snaphots', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('snaphot_id')->nullable();
          $table->string('stay_id')->nullable();
          $table->integer('scan_range')->nullable();
          $table->string('currency',5)->nullable();
          $table->string('date_start')->nullable();
          $table->text('content')->nullable();
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
