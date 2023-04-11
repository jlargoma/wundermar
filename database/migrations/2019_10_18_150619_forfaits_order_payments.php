<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForfaitsOrderPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('forfaits_order_payments', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('order_id')->nullable();
          $table->unsignedBigInteger('book_id')->nullable();
          $table->string('cli_email')->nullable();
          $table->string('subject')->nullable();
          $table->string('order_uuid')->nullable();
          $table->string('order_created')->nullable();
          $table->float('amount', 8, 2)->nullable();
          $table->integer('refunded');
          $table->integer('currency');
          $table->boolean('paid')->nullable();
          $table->string('additional');
          $table->string('service');
          $table->string('status');
          $table->string('token');
          $table->text('transactions')->nullable();
          $table->string('client_uuid');
          $table->string('form_token')->nullable();
          $table->string('key_token')->nullable();
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
