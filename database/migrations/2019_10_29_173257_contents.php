<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Contents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('contents', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('key')->nullable();
          $table->string('field')->nullable();
          $table->text('content')->nullable();
          
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
