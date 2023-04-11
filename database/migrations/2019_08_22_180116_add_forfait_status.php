<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForfaitStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('forfaits_users', function($table) {
        $table->integer('ffexpr_status')->nullable();
        $table->integer('ffexpr_bookingNumber')->nullable();
        $table->text('ffexpr_data')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('forfaits_users', function($table) {
        $table->integer('ffexpr_status');
        $table->integer('ffexpr_bookingNumber');
        $table->text('ffexpr_data');
      });
    }
}
