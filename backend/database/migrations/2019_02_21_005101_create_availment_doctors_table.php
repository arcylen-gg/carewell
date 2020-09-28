<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailmentDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availment_doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('availment_id')->unsigned();
            $table->foreign('availment_id')->references('id')->on('availments')->onDelete('cascade');
            $table->integer('doctor_id')->unsigned()->nullable();
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->integer('specialization_id')->unsigned()->nullable();
            $table->foreign('specialization_id')->references('id')->on('specializations')->onDelete('cascade');
            $table->string('rate_rvs')->nullable();
            $table->integer('procedures_id')->unsigned()->nullable();
            $table->foreign('procedures_id')->references('id')->on('procedures')->onDelete('cascade');
            $table->double('doctors_fee')->default(0);
            $table->double('phic_charity_swa')->default(0);
            $table->double('patient_charged')->default(0);
            $table->double('carewell_charged')->default(0);
            $table->double('charge_other')->default(0);
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
        Schema::dropIfExists('availment_doctors');
    }
}
