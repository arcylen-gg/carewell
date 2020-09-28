<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayableProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payable_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payable_id')->unsigned();
            $table->foreign('payable_id')->references('id')->on('payables')->onDelete('cascade');
            $table->integer('payee_id')->unsigned();
            $table->foreign('payee_id')->references('id')->on('provider_payees')->onDelete('cascade');
            $table->integer('bank_id')->nullable()->unsigned();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->date('release_date')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('check_voucher_number')->nullable();
            $table->double('amount');
            $table->string('received_by')->nullable();
            $table->string('reference_approval_numbers')->nullable();
            $table->integer('save_as_draft')->unsigned()->default(0)->comment('0 = saved, 1 = draft');
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
        Schema::dropIfExists('payable_providers');
    }
}
