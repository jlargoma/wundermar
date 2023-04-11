<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExcursion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('excursions', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name')->nullable();
          $table->string('title')->nullable();
          $table->text('content')->nullable();
          $table->integer('tag')->nullable();
          $table->string('img')->nullable();
          $table->text('imgs')->nullable();
          $table->string('video')->nullable();
          $table->string('meta_title')->nullable();
          $table->string('meta_descript')->nullable();
          $table->string('canonical')->nullable();
          $table->integer('pos')->nullable();
          $table->integer('price')->nullable();
          $table->integer('price_basic')->nullable();
          $table->integer('starts')->nullable();
          $table->integer('status')->nullable();
          $table->timestamps();
       });
       
       Schema::create('excursions_orders', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name')->nullable();
          $table->string('mail')->nullable();
          $table->string('phone')->nullable();
          $table->date('start')->nullable();
          $table->date('end')->nullable();
          $table->string('excursions')->nullable();
          $table->integer('adults')->nullable();
          $table->integer('childrens')->nullable();
          $table->integer('starts')->nullable();
          $table->integer('status')->nullable();
          $table->text('observ')->nullable();
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
