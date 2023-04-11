<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColmPartee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('book_partees', function (Blueprint $table) {
        $table->string('date_complete')->nullable();
        $table->string('date_finish')->nullable();
        $table->boolean('has_checked')->nullable()->default(0);
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
