<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MultiSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::dropIfExists('rules_stripe');
      Schema::dropIfExists('dayssecondpay');
      Schema::dropIfExists('extrasbooks');
      Schema::dropIfExists('extras');
      
      Schema::create('extra_prices', function (Blueprint $table) {
        
        $table->bigIncrements('id');
        $table->string('name')->nullable();
        $table->double('price', 8, 2)->nullable();
        $table->double('cost', 8, 2)->nullable();
        $table->string('channel_group',8)->nullable();
        $table->boolean('fixed')->nullable();
        $table->boolean('deleted')->nullable();
        
        $table->timestamps();
       
      });
      
      Schema::create('book_extra_prices', function (Blueprint $table) {
        
        $table->bigIncrements('id');
        $table->integer('book_id')->nullable();
        $table->integer('extra_id')->nullable();
        $table->integer('qty')->nullable();
        $table->double('price', 8, 2)->nullable();
        $table->double('cost', 8, 2)->nullable();
        $table->string('channel_group',8)->nullable();
        $table->boolean('fixed')->nullable();
        $table->boolean('deleted')->nullable();
        $table->string('status',10)->nullable();
        $table->string('vdor')->nullable();
        
        $table->timestamps();
       
      });
      
      Schema::table('payment_orders', function (Blueprint $table) {
         $table->integer('site_id',5)->nullable();
      });
      Schema::table('book_orders', function (Blueprint $table) {
         $table->integer('site_id',5)->nullable();
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
