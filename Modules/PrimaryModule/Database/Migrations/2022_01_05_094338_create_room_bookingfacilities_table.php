<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomBookingfacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_bookingfacilities', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_id');
            $table->string('room_id');
            $table->string('facility_id');
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
        Schema::dropIfExists('room_bookingfacilities');
    }
}
