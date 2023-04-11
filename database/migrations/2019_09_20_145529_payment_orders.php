<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentOrders extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('payment_orders', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('book_id')->nullable();
          $table->integer('cli_id')->nullable();
          $table->string('cli_email')->nullable();
          $table->integer('amount');
          $table->string('status');
          $table->string('token');
          $table->string('data')->nullable();
          $table->string('description')->nullable();
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
      Schema::drop('payment_orders');
    }
}
