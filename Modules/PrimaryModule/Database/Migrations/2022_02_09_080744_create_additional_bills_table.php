<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_bills', function (Blueprint $table) {
            $table->increments('additional_bill_id');
            $table->string('bill_no');
            $table->string('bill_type')->comment('1 = housekeeping 2 = additional service 3 = KOT bill 4 = BOT bill ');
            $table->bigInteger('res_id');
            $table->bigInteger('room_id')->comment('0 = all the rooms in the reservation');
            $table->bigInteger('created_by');
            $table->date('date');
            $table->double('amount');
            $table->string('department')->comment('1 = housekeeping 2 = additional service 3 = kitchen 4 = bar ');
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
        Schema::dropIfExists('additional_bills');
    }
}
