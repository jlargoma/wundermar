<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('bank', function (Blueprint $table) {
            $table->increments('id');
            $table->string('concept');
            $table->date('date');
            $table->decimal('import', 15,2);
            $table->string('comment');
            $table->string('typePayment');
            $table->integer('type')->default(0)->comment = "0 = ingresa; 1 = paga";
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
