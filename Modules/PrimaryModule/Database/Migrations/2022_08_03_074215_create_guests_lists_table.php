<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestsListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guests_lists', function (Blueprint $table) {
            $table->id();
            $table->string('passport_id');
            $table->string('guestFname');
            $table->string('guestLname');
            $table->longText('guestAddress',255)->nullable();
            $table->string('guestEmail')->nullable();
            $table->string('gcountry');
            $table->string('contactNo')->nullable();
            $table->string('guesttype')->nullable();

            $table->date('dob')->nullable();
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
        Schema::dropIfExists('guests_lists');
    }
}
