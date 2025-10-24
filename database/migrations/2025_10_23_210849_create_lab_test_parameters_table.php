<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabTestParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_test_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_test_cat_id'); // this column must exist
            $table->string('parameter_name');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->timestamps();

            $table->foreign('lab_test_cat_id')->references('id')->on('labtest_cat')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_test_parameters');
    }
}
