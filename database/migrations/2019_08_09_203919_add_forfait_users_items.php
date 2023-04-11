<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForfaitUsersItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//      Schema::create('forfaits_items_users', function (Blueprint $table) {
//          $table->bigIncrements('id');
//          $table->integer('item_id')->unsigned();
//          $table->foreign('item_id')->references('id')->on('forfaits_items');
//          $table->integer('user_id')->unsigned();
//          $table->foreign('user_id')->references('id')->on('forfaits_users');
//          $table->integer('type');
//          $table->integer('quantity');
//          $table->integer('years');
//          $table->date('date_start')->nullable();
//          $table->date('date_end');
//          $table->string('status');
//          $table->string('price');
//          $table->integer('guestNumber')->nullable();
//          $table->boolean('sentSMS')->nullable();
//          $table->text('long_data');
//          $table->timestamps();
//      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//      Schema::drop('forfaits_items_users');
    }
}
