<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('daily_prices', function(Blueprint $table)
      {
          $table->string('channel_group',8)->nullable();
          $table->date('date')->nullable();
          $table->double('price', 8, 2)->nullable();
          $table->integer('min_estancia')->nullable();
          $table->integer('user_id')->nullable();

          $table->primary(['channel_group', 'date']);
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
