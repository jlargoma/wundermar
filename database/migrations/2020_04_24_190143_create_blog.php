<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('blogs', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name')->nullable();
          $table->string('title')->nullable();
          $table->text('content')->nullable();
          $table->string('img')->nullable();
          $table->string('meta_title')->nullable();
          $table->string('meta_descript')->nullable();
          $table->string('canonical')->nullable();
          $table->integer('pos')->nullable();
          $table->integer('status')->nullable();
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
