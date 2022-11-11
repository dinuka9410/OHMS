<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id');
            $table->integer('Room_id');
            $table->bigInteger('disscount');
            $table->integer('diss_Type')->comment('1 = presentage, 2 = amount');
            $table->date('checkout_date');
            $table->string('rate');
            $table->string('total');
            $table->string('addtional_total');
            $table->string('finaltotal_Total');
            $table->string('subtotal_Total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_rooms');
    }
}
