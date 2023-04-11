<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtramailCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('customers', function (Blueprint $table) {
        $table->boolean('send_mails')->nullable();
        $table->boolean('send_notif')->nullable();
        $table->string('email_notif')->nullable();
      });
      Schema::table('book', function (Blueprint $table) {
        $table->string('priceOTA')->nullable();
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
