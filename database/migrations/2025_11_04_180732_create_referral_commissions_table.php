<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id');
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('patient_id');
            $table->decimal('bill_amount', 12, 2)->comment('Original bill amount');
            $table->decimal('commission_percentage', 5, 2)->comment('Commission percentage applied');
            $table->decimal('commission_amount', 12, 2)->comment('Calculated commission amount');
            $table->string('status')->default('pending')->comment('pending, paid, cancelled');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('referral_id')->references('id')->on('referrals')->onDelete('cascade');
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

            // Indexes for better query performance
            $table->index('referral_id');
            $table->index('bill_id');
            $table->index('patient_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_commissions');
    }
}
