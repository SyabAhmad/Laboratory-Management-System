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
        Schema::table('lab_test_parameters', function (Blueprint $table) {
            // Add new fields for dual-option support
            $table->enum('field_type', ['text', 'number', 'dual_option', 'textarea'])->default('text')->after('reference_range');
            $table->json('dual_options')->nullable()->after('field_type'); // Store options like ["Positive", "Negative"]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_test_parameters', function (Blueprint $table) {
            $table->dropColumn(['field_type', 'dual_options']);
        });
    }
};