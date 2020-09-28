<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('universal_id')->nullable();
            $table->string('carewell_id')->nullable();
            $table->string('company_ref_id')->nullable();
            $table->integer('member_id')->nullable()->unsigned();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->integer('company_id')->nullable()->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('company_deployment_id')->nullable()->unsigned();
            $table->foreign('company_deployment_id')->references('id')->on('company_deployments')->onDelete('cascade');
            $table->integer('payment_mode_id')->nullable()->unsigned();
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('cascade');
            $table->integer('coverage_plan_id')->nullable()->unsigned();
            $table->foreign('coverage_plan_id')->references('id')->on('coverage_plans')->onDelete('cascade');
            $table->date('policy_date')->nullable();
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
        Schema::dropIfExists('member_companies');
    }
}
