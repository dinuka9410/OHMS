<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalFacilitiesTable extends Migration
{
    /**
     * Run the migrations. 
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_additional_facilities', function (Blueprint $table) {
            $table->increments('additional_facilities_id')->comment('additional_facilities_Id');
            $table->integer('room_id')->comment('room Id');
            $table->integer('facilities')->comment('facilities_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_additional_facilities');
    }
}
