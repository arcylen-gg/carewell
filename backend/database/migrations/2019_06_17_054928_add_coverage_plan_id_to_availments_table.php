<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoveragePlanIdToAvailmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('availments', function (Blueprint $table) {
            //
            $table->integer('coverage_plan_id')->unsigned()->nullable()->after('provider_id');
            $table->foreign('coverage_plan_id')->references('id')->on('coverage_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availments');
        // Schema::table('availments', function (Blueprint $table) {
        //     //
        // });
    }
}
