<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAvailmentDiagnosis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('availments', function (Blueprint $table) {
            $table->dropForeign('availments_diagnosis_id_foreign');
            $table->integer('diagnosis_id')->nullable()->unsigned()->change();
            $table->foreign('diagnosis_id')->references('id')->on('diagnosis_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
