<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('res_id');
            $table->string('invo_code');
            $table->integer('debtor_id');
            $table->bigInteger('created_by');
            $table->integer('debtor_type')->comment(' 1 = guest, 2 = travel agent ');
            $table->string('final_room');
            $table->string('final_addtional');
            $table->string('final_subtotal');
            $table->string('final_grandtotal')->comment('final amount after dicount service tax reduced');
            $table->string('discount')->comment(' room bill discount as a percentage');
            $table->double('discount_type')->comment(' 1=% , 2=amount');
            $table->date('invoice_date');
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
        Schema::dropIfExists('invoices');
    }
}
