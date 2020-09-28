<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosisListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diagnosis_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longtext('description');
            $table->integer('parent_id')->unsigned()->nullable()->default(0);
            $table->foreign('parent_id')->references('id')->on('diagnosis_lists')->onDelete('cascade');
            $table->integer('level')->default(0);
            $table->tinyInteger('covered_first_year')->default(0);
            $table->tinyInteger('covered_after_year')->default(0);
            $table->tinyInteger('exclusion')->default(0);
            $table->tinyInteger('is_archived')->default(0);
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
        Schema::dropIfExists('diagnosis_lists');
    }
}
