<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasLowProfit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('book', function($table) {
        $table->boolean('has_low_profit')->default(FALSE);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('book', function($table) {
          $table->dropColumn('has_low_profit');
      });
    }
}
