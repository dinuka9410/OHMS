<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomallocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roomallocations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('res_id')->comment('reservation id');
            $table->string('roomNumber');
            $table->bigInteger('basis')->comment('meal plan id');
            $table->double('rate')->comment('room rate for the allocation');
            $table->date('date');
            $table->integer('status')->comment('1 = active 5 = overwrite by other ');
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
        Schema::dropIfExists('roomallocations');
    }
}
