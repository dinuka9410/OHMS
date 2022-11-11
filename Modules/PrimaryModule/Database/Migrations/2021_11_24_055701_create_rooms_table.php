<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('room_id')->comment('room Id');
            $table->text('room_name')->comment('room rame');
            $table->integer('room_area')->comment('room area');
            $table->integer('room_category')->comment('room category');
            $table->integer('room_max_recident')->comment('max recident');
            $table->integer('room_default_recident')->comment('default recident');
            $table->integer('room_max_adults')->comment('max allowed adults');
            $table->integer('room_max_children')->comment('max allowed children');
            $table->integer('room_beds')->comment('number of beds in the room');
            $table->text('room_floor')->comment('floor number');
            $table->integer('room_status')->comment('floor number');
            $table->text('room_descrption')->nullable()->comment('descrption');
            $table->integer('room_type_id')->comment('room Type Id');
            $table->integer('Status')->comment('0=inactive 1 = active');
            $table->dateTime('Create_date')->comment('user added Date Time');
            $table->dateTime('Update_date')->comment('user added Date Time');
            $table->integer('create_by')->comment('user added Date Time');
            $table->integer('update_by')->comment('user added Date Time');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
