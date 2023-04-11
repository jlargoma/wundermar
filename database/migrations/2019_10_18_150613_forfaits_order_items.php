<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForfaitsOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
      Schema::create('forfaits_order_items', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('order_id')->nullable();
          $table->string('type')->nullable();
          $table->text('data')->nullable();
          $table->text('forfait_users')->nullable();
          $table->float('total', 8, 2)->nullable();
          $table->float('extra', 8, 2)->nullable();
          $table->string('status')->nullable();
          $table->boolean('cancel')->nullable();
          $table->integer('ffexpr_status')->nullable();
          $table->integer('ffexpr_bookingNumber')->nullable();
          $table->text('ffexpr_data')->nullable();
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
