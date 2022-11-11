<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddAdditionalFacilitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_additional_facilites', function (Blueprint $table) {
            $table->increments('add_additional_facilites_id')->comment('add additional facilities Id');
            $table->text('add_additional_facilites_name')->comment('add additional facilites name');
            // $table->text('updated_at')->comment('add additional facilites name');
            // $table->date('facilites_number')->comment('facilities_number');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_additional_facilites');
    }
}
