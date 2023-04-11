<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('customers', function ($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('seasons', function ($table) {
            $table->foreign('type')->references('id')->on('typeseasons');
        });
        Schema::table('prices', function ($table) {
            $table->foreign('season')->references('id')->on('typeseasons');
        });
        Schema::table('rooms', function ($table) {            
            $table->foreign('owned')->references('id')->on('users');
             $table->foreign('sizeApto')->references('id')->on('sizerooms');
        });
        
        Schema::table('book', function ($table) {
           $table->foreign('user_id')->references('id')->on('users');
           $table->foreign('customer_id')->references('id')->on('customers');
           $table->foreign('room_id')->references('id')->on('rooms');
        });

        Schema::table('paymentspro', function ($table) {
           $table->foreign('room_id')->references('id')->on('rooms');
        });

        Schema::table('payments', function ($table) {
           $table->foreign('book_id')->references('id')->on('book');
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
