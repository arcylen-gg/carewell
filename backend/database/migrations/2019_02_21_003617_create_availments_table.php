<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('approval_id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('member_id')->unsigned();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->string('carewell_id');
            $table->integer('provider_id')->unsigned();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->integer('benefit_type_id')->unsigned();
            $table->foreign('benefit_type_id')->references('id')->on('benefit_types')->onDelete('cascade');
            $table->date('date_avail');
            $table->longText('chief_complaint');
            $table->longText('initial_diagnosis');
            $table->longText('final_diagnosis');
            $table->integer('diagnosis_id')->unsigned()->nullable();
            $table->foreign('diagnosis_id')->references('id')->on('diagnosis_lists')->onDelete('cascade');
            $table->integer('provider_payee_id')->unsigned();
            $table->foreign('provider_payee_id')->references('id')->on('provider_payees')->onDelete('cascade');
            $table->integer('doctor_id')->unsigned()->nullable();
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->double('provider_payee_fee')->nullable();
            $table->double('doctor_fee')->nullable();
            $table->string('caller_name')->nullable();
            $table->string('caller_position')->nullable();
            $table->date('caller_date');
            $table->string('caller_contact_number')->nullable();
            $table->string('status')->default('new');
            $table->string('status_remarks')->nullable();
            $table->double('procedures_gross_amount_total')->default(0);
            $table->double('procedures_phic_amount_total')->default(0);
            $table->double('procedures_patient_charged_total')->default(0);
            $table->double('procedures_carewell_charged_total')->default(0);
            $table->double('procedures_charge_other_total')->default(0);
            $table->double('doctors_gross_amount_total')->default(0);
            $table->double('doctors_phic_amount_total')->default(0);
            $table->double('doctors_patient_charged_total')->default(0);
            $table->double('doctors_carewell_charged_total')->default(0);
            $table->double('doctors_charge_other_total')->default(0);
            $table->double('grand_total')->default(0);
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
        Schema::dropIfExists('availments');
    }
}
