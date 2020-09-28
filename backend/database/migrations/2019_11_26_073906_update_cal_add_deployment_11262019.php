<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCalAddDeployment11262019 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cals', function (Blueprint $table) {
            $table->integer('deployment_id')->unsigned()->after('company_id')->nullable();

            $table->foreign('deployment_id')->references('id')->on('company_deployments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cals', function (Blueprint $table) {
            //
        });
    }
}
