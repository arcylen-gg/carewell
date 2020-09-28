<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cal_id')->unsigned();
            $table->foreign('cal_id')->references('id')->on('cals')->onDelete('cascade');
            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->integer('deployment_id')->unsigned();
            $table->foreign('deployment_id')->references('id')->on('company_deployments')->onDelete('cascade');
            $table->string('carewell_id')->nullable();
            $table->double('monthly_premium');
            $table->integer('period_count');
            $table->date('date_paid');
            $table->double('paid_amount')->default(0);
            $table->string('status');
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
        Schema::dropIfExists('cal_members');
    }
}
