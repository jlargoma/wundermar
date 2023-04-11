<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForfaitsOrderPaymentLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
      Schema::create('forfaits_order_payment_links', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('book_id')->nullable();
          $table->integer('order_id')->nullable();
          $table->string('cli_email')->nullable();
          $table->string('subject')->nullable();
          $table->float('amount', 8, 2)->nullable();
          $table->integer('currency');
          $table->boolean('paid')->nullable();
          $table->string('status')->nullable();
          $table->string('token');
          $table->integer('last_item_id')->nullable();
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
