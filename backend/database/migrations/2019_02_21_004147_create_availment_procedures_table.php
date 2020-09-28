<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailmentProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availment_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('availment_id')->unsigned();
            $table->foreign('availment_id')->references('id')->on('availments')->onDelete('cascade');
            $table->integer('procedures_id')->unsigned()->nullable();
            $table->foreign('procedures_id')->references('id')->on('procedures')->onDelete('cascade');
            $table->double('gross_amount')->default(0);
            $table->double('phic_charity_swa')->default(0);
            $table->double('patient_charged')->default(0);
            $table->double('carewell_charged')->default(0);
            $table->double('charge_other')->default(0);
            $table->string('remarks')->nullable();
            $table->tinyInteger('is_disapproved')->nullable();
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
        Schema::dropIfExists('availment_procedures');
    }
}
