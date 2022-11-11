<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_rates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('agent_id');
            $table->bigInteger('season_id');
            $table->bigInteger('meal_plan_id');
            $table->bigInteger('room_type_id');
            $table->bigInteger('room_category');
            $table->double('rate')->nullable();
            $table->double('discount')->nullable();
            $table->string('status');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->timestamps();

           //$table->unique(['agent_id','meal_plan_id','season_id','room_type_id','room_category'],'test');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_rates');
    }
}
