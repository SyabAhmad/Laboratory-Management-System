<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientRegistrationReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('receipt_number')->unique(); // Token/barcode number
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->json('tests')->nullable(); // Stores test details with prices and status
            $table->enum('status', ['draft', 'printed', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            $table->string('printed_by')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            
            // Indexes
            $table->index('receipt_number');
            $table->index('patient_id');
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
        Schema::dropIfExists('patient_receipts');
    }
}
