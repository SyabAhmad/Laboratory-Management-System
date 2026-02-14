<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id')->nullable();
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('doctor_name');
            $table->decimal('bill_amount', 10, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('referral_id')
                ->references('id')
                ->on('referrals')
                ->onDelete('set null');

            $table->foreign('bill_id')
                ->references('id')
                ->on('bills')
                ->onDelete('set null');

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('set null');

            // Indexes
            $table->index('status');
            $table->index('paid_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_commissions');
    }
};
