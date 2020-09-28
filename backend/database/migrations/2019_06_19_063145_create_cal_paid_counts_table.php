<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalPaidCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cal_paid_counts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cal_id')->unsigned();
            $table->foreign('cal_id')->references('id')->on('cals')->onDelete('cascade');
            $table->integer('cal_member_id')->unsigned();
            $table->foreign('cal_member_id')->references('id')->on('cal_members')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::dropIfExists('cal_paid_counts');
    }
}
