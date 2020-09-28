<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayableAvailmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payable_availments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payable_id')->unsigned();
            $table->foreign('payable_id')->references('id')->on('payables')->onDelete('cascade');
            $table->integer('availment_id')->unsigned();
            $table->foreign('availment_id')->references('id')->on('availments')->onDelete('cascade');
            $table->double('payable_amount')->default(0);
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('payable_availments');
    }
}
