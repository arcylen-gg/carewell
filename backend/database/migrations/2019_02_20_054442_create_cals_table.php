<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cal_number');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('payment_term_id')->unsigned();
            $table->foreign('payment_term_id')->references('id')->on('payment_terms')->onDelete('cascade');
            $table->integer('payment_mode_id')->unsigned();
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes')->onDelete('cascade');
            $table->date('payment_start_date');
            $table->date('payment_end_date');
            $table->double('total_amount_paid')->nullable();
            $table->string('status');
            $table->string('reference_number')->nullable();
            $table->date('payment_cal_date');
            $table->date('payment_due_date');
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
        Schema::dropIfExists('cals');
    }
}
