<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoveragePlanBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coverage_plan_benefits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coverage_plan_id')->unsigned();
            $table->foreign('coverage_plan_id')->references('id')->on('coverage_plans')->onDelete('cascade');
            $table->integer('benefit_type_id')->unsigned();
            $table->foreign('benefit_type_id')->references('id')->on('benefit_types')->onDelete('cascade');
            $table->string('charges');
            $table->double('covered_amount');
            $table->tinyInteger('per_confinement')->default(0);
            $table->double('per_confinement_amount');
            $table->integer('limit_per_year');
            $table->integer('limit_per_month');
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
        Schema::dropIfExists('coverage_plan_benefits');
    }
}
