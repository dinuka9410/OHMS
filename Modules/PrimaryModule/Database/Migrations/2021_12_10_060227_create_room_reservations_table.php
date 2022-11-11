<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->date('resDate');
            $table->date('checkinDate');
            $table->date('checkoutDate');
            $table->bigInteger('agent_id');
            $table->bigInteger('season_id');
            $table->bigInteger('guest_id');
            $table->longText('remarks')->nullable();
            $table->integer('status')->comment('0 = pending 1 = confirmed 2 = checked-in 3 = checkout 4 = cancelled  5 = overwrite by other ');
            $table->longText('booking_id')->nullable();
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
        Schema::dropIfExists('room_reservations');
    }
}
