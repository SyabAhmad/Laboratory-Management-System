<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_name');
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->string('month'); // e.g. '2026-02'
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->string('payment_method')->default('cash');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_payments');
    }
};
