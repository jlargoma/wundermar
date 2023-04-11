<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('book_id');

            $table->date('start')->after('status')->nullable();
            $table->date('finish')->after('start')->nullable();
            $table->decimal('total_price',8,2)->after('status')->nullable();

            $table->string('name_business')->after('finish')->nullable();
            $table->string('nif_business')->after('name_business')->nullable();
            $table->string('address_business')->after('nif_business')->nullable();
            $table->integer('phone_business')->after('address_business')->nullable();
            $table->string('zip_code_business')->after('phone_business')->nullable();

        });


        Schema::table('users', function (Blueprint $table) {

            $table->string('nif')->after('phone')->nullable();
            $table->string('address')->after('phone')->nullable();
            $table->string('zip_code')->after('phone')->nullable();

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
