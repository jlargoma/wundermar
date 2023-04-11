<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
//DROP TABLE  forfaits_order_payment_links;   
//DROP TABLE  forfaits_errors; 
//DROP TABLE  forfaits_orders; 
//DROP TABLE  forfaits_order_items; 
//DROP TABLE  forfaits_order_payments; 
//  
class ForfaitsErrors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('forfaits_errors', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('book_id');
          $table->unsignedBigInteger('order_id');
          $table->integer('item_id')->nullable();
          $table->string('detail')->nullable();
          $table->text('more_info')->nullable();
          $table->boolean('watched')->default(0);
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
