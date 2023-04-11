<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForfait extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('forfaits_orders', function (Blueprint $table) {
        $table->integer('forfats_id')->nullable();
        $table->integer('quantity')->nullable();
        $table->string('detail')->nullable();
        $table->string('type')->nullable();
      });
      
      Schema::create('forfaits', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('book_id')->nullable();
          $table->unsignedBigInteger('cli_id')->nullable();
          $table->string('name')->nullable();
          $table->string('email')->nullable();
          $table->string('phone')->nullable();
          $table->text('more_info')->nullable();
          $table->float('total', 8, 2)->nullable();
          $table->string('status')->nullable();
          
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
