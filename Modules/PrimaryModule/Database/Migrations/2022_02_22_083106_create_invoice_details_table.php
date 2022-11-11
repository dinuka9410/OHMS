<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->increments('inv_details_id');
            $table->bigInteger('inv_id');
            $table->string('bill_no')->nullable();
            $table->integer('bill_type')->comment('0 = room bill 1 = House keeping bill 2 = Additional service bill 3 = Kitchen bill 4 = Bar Bill');
            $table->bigInteger('room_id')->comment('0 = all rooms');
            $table->double('rate');
            $table->double('amount');
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
        Schema::dropIfExists('invoice_details');
    }
}
