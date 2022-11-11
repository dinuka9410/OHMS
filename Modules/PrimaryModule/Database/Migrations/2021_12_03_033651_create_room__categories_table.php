<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_categories', function (Blueprint $table) {
            $table->increments('room_categories_id')->comment('room_categories_id');
            $table->text('room_categories_name')->comment('room_categories_name');
            $table->integer('area')->comment('room area');
            $table->integer('max_recident')->comment('max recident');
            $table->integer('default_recident')->comment('default recident');
            $table->integer('max_adults')->comment('max allowed adults');
            $table->integer('max_children')->comment('max allowed children');
            $table->integer('status')->comment('room categories status');
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
        Schema::dropIfExists('room_categories');
    }
}
