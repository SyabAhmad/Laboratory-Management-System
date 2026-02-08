<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('doctor_commissions');
        Schema::create('doctor_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id')->nullable();
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('doctor_name');
            $table->decimal('bill_amount', 12, 2)->default(0);
            $table->decimal('commission_percentage', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_commissions');
    }
};
