<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomCatAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_cat_amounts', function (Blueprint $table) {
            $table->increments('room_cat_amounts_id')->comment('room_cat_amounts id');
            $table->integer('room_type_id')->comment('room type id');
            $table->integer('room_categories_id')->comment('room categories id');
            $table->integer('room_cat_amounts_amount')->comment('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_cat_amounts');
    }
}
