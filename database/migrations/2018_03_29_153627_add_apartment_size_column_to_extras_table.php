<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema;

class AddApartmentSizeColumnToExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('extras', function (Blueprint $t) {
            $t->tinyInteger('apartment_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('extras', function (Blueprint $t) {
            $t->dropColumn('apartment_size');
        });
    }
}
