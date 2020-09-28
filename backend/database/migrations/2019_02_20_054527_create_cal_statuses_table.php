<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cal_id')->unsigned();
            $table->foreign('cal_id')->references('id')->on('cals')->onDelete('cascade');
            $table->integer('bank_id')->unsigned()->nullable();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->date('pending_date')->nullable();
            $table->string('pending_remarks')->nullable();
            $table->date('collection_date')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->integer('or_number')->nullable();
            $table->double('amount')->nullable();
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
        Schema::dropIfExists('cal_statuses');
    }
}
