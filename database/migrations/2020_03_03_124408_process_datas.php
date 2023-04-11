<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProcessDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('processed_data', function (Blueprint $table) {
        
        $table->bigIncrements('id');
        $table->string('key')->nullable();
        $table->string('name')->nullable();
        $table->text('content')->nullable();
        $table->boolean('deleted')->nullable();
        
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
