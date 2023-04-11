<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForfaitItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('forfaits_items', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('item_key');
          $table->string('cat');
          $table->string('name');
          $table->string('type')->nullable();
          $table->string('equip')->nullable();
          $table->string('class')->nullable();
          $table->boolean('status')->nullable();
          $table->integer('regular_price')->nullable();
          $table->integer('special_price')->nullable();
          $table->text('log_data');
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
      Schema::drop('forfaits_items');
    }
}
