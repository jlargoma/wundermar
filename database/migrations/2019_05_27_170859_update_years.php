<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateYears extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('years', function (Blueprint $table) {
		    $table->date('start_date');
		    $table->date('end_date');

	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('years', function($table) {
		    $table->date('start_date');
		    $table->date('end_date');
	    });
    }
}
