<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WobookAvails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('wobook_avails', function(Blueprint $table)
      {
        $table->bigIncrements('id');
        $table->string('channel_group',8)->nullable();
        $table->date('date')->nullable();
        $table->integer('avail')->nullable();
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
