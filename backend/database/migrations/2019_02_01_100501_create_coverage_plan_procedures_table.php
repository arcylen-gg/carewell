<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoveragePlanProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coverage_plan_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('procedure_type_id')->unsigned();
            $table->foreign('procedure_type_id')->references('id')->on('procedure_types')->onDelete('cascade');
            $table->integer('procedure_id')->unsigned();
            $table->foreign('procedure_id')->references('id')->on('procedures')->onDelete('cascade');
            $table->integer('coverage_plan_benefit_id')->unsigned();
            $table->foreign('coverage_plan_benefit_id')->references('id')->on('coverage_plan_benefits')->onDelete('cascade');
            $table->double('amount')->default(0);
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
        Schema::dropIfExists('coverage_plan_procedures');
    }
}
