<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_rooms', function (Blueprint $table) {
            $table->increments('g_res_room_id');
            $table->bigInteger('res_id');
            $table->bigInteger('guest_id')->nullable();
            $table->bigInteger('guest_List_id')->nullable();
            $table->string('room_no');
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
        Schema::dropIfExists('guest_rooms');
    }
}
