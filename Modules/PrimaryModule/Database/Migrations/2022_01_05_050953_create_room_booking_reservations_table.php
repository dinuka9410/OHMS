<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomBookingReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_booking', function (Blueprint $table) {
            $table->increments('booking_reservations_id')->comment('booking reservations id');
            $table->string('code');
            $table->date('resDate');
            $table->date('booking_checkinDate');
            $table->date('booking_checkoutDate');
            $table->bigInteger('agent_id');
            $table->bigInteger('guest_id');
            $table->bigInteger('season_id');
            $table->longText('remarks')->nullable();
            $table->longText('user_remarks')->nullable();
            $table->integer('status')->comment('0 = pending 1 = confirmed 2 = checked-in 3 = checkout 4 = cancelled 5 = overwritten ');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
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
        Schema::dropIfExists('room_booking');
    }
}
