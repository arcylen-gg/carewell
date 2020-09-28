<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoveragePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coverage_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre_existing_id')->unsigned();
            $table->foreign('pre_existing_id')->references('id')->on('pre_existings')->onDelete('cascade');
            $table->string('name');
            $table->string('age_bracket');
            $table->double('processing_fee')->default(0);
            $table->double('hospital_income_benefit')->default(0);
            $table->double('monthly_premium')->default(0);
            $table->double('handling_fee')->default(0);
            $table->double('card_fee')->default(0);
            $table->double('annual_benefit_limit')->default(0);
            $table->double('maximum_benefit_limit')->default(0);
            $table->string('max_limit_per');
            $table->tinyInteger('is_archived')->default(0);
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
        Schema::dropIfExists('coverage_plans');
    }
}
