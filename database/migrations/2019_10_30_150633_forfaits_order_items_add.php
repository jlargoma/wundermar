<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForfaitsOrderItemsAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('forfaits_order_items', function (Blueprint $table) {
         $table->float('price_wd')->nullable(); //price without discount
         $table->text('insurances')->nullable(); 
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
