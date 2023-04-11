<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bail', function (Blueprint $table) {
            $table->increments('id_book')->unsigned();
            $table->decimal('import_in');
            $table->date('date_in');
            $table->string('comment_in');
            $table->decimal('import_out');
            $table->date('date_out');
            $table->string('comment_out');
            $table->timestamps();
        });


        Schema::table('bail', function ($table) {            
            $table->foreign('id_book')->references('id')->on('book');
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
