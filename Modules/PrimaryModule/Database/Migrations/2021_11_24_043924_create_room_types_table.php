<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->increments('room_type_id')->comment('room type Id');
            $table->text('room_type_Select')->comment('room Type selected');
            $table->text('room_type_descrption')->comment('descrption');
            $table->integer('room_type_status')->comment('room status');
            $table->text('created_by')->comment('added user');
            $table->dateTime('created_at')->comment('user added Date Time');
            $table->text('updated_by')->comment('added user');
            $table->dateTime('updated_at')->comment('user added Date Time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_types');
    }
}
