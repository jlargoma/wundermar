<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChannelManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('channel_manager_queues', function(Blueprint $table)
      {
          $table->bigIncrements('id');
          $table->string('channel_group',8)->nullable();
          $table->date('date_start')->nullable();
          $table->date('date_end')->nullable();
          $table->boolean('avail');
          $table->double('price', 8, 2)->nullable();
          $table->integer('minimumStay')->nullable();
          $table->string('weekDays')->nullable();
          $table->boolean('sent');
          $table->timestamps();
         
      });
      Schema::create('book_visa', function(Blueprint $table)
      {
          $table->bigIncrements('id');
          $table->integer('book_id')->nullable();
          $table->integer('customer_id')->nullable();
          $table->integer('user_id')->nullable();
          $table->text('visa_data')->nullable();
          $table->date('sent_date')->nullable();
          $table->integer('imported',2)->nullable();
          $table->timestamps();
         
      });
//      
      Schema::table('book', function (Blueprint $table) {
         $table->string('external_id',50)->nullable();
         $table->string('propertyId',50)->nullable();
         $table->string('external_roomId',50)->nullable();
      });
      
      Schema::table('rooms', function (Blueprint $table) {
         $table->string('channel_group',8)->nullable();
         $table->double('price_extra_pax', 8, 2)->nullable();
      });
//      
//      Schema::table('customers', function (Blueprint $table) {
//         $table->string('surname')->nullable();
//         $table->string('language')->nullable();
//      });
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
