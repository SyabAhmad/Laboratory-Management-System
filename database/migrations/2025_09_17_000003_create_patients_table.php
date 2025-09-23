<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('address')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('note')->nullable();
            $table->string('bp')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('registerd_by')->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->timestamps();

            $table->foreign('referred_by')->references('id')->on('referrals')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
