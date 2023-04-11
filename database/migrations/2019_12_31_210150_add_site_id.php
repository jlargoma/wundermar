<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('sities', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name')->nullable();
        $table->string('status')->nullable();
        $table->string('url')->nullable();
        $table->string('title')->nullable();
        $table->string('site')->nullable();
        $table->string('mail_name')->nullable();
        $table->string('mail_from')->nullable();
      });
      
      Schema::table('contents', function (Blueprint $table) {
        $table->integer('site_id')->nullable();
      });
      
      Schema::table('rooms', function (Blueprint $table) {
        $table->integer('site_id')->nullable();
      });
      
      Schema::table('settings', function (Blueprint $table) {
        $table->integer('site_id')->nullable();
      });
      
      Schema::table('rooms_types', function (Blueprint $table) {
        $table->integer('site_id')->nullable();
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
