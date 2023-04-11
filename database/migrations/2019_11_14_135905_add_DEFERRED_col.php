<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDEFERREDCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('book_orders', function (Blueprint $table) {
        $table->boolean('is_deferred')->nullable()->default(0);
        $table->boolean('was_confirm')->nullable()->default(0);
        $table->float('payment')->nullable()->default(0);
      });
      Schema::table('payment_orders', function (Blueprint $table) {
        $table->boolean('is_deferred')->nullable()->default(0);
        $table->boolean('was_confirm')->nullable()->default(0);
        $table->float('payment')->nullable()->default(0);
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
