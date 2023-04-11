<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomerAceptDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('customers', function (Blueprint $table) {
        $table->timestamp('accepted_hiring_policies')->nullable();
        $table->timestamp('accepted_bail_conditions')->nullable();
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
