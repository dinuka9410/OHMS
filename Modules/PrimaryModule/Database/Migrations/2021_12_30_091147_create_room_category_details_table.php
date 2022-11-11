<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomCategoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_category_details', function (Blueprint $table) {
            $table->increments('room_category_details_id')->comment('room category details id');
            $table->integer('room_type_id')->comment('room type id');
            $table->integer('room_categories_id')->comment('room categories id');
            $table->integer('room_type_area')->comment('room area');
            $table->integer('room_type_max_recident')->comment('max recident');
            $table->integer('room_type_default_recident')->comment('default recident');
            $table->integer('room_type_max_adults')->comment('max allowed adults');
            $table->integer('room_type_max_children')->comment('max allowed children');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_category_details');
    }
}
