<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('agentCode',255);
            $table->string('agentName',255);
            $table->string('agentEmail',255);
            $table->longText('agentAddress',255);
            $table->string('agentRating',20);
            $table->string('agentContactPerson',255);
            $table->string('tel_no_1',255);
            $table->string('tel_no_2',255)->nullable();
            $table->integer('status');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
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
        Schema::dropIfExists('agents');
    }
}
