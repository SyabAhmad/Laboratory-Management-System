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
        Schema::table('bills', function (Blueprint $table) {
            // Add all_test column to store test details as JSON
            if (!Schema::hasColumn('bills', 'all_test')) {
                $table->json('all_test')->nullable()->after('status');
            }
            
            // Add other missing columns that the view expects
            if (!Schema::hasColumn('bills', 'bill_no')) {
                $table->string('bill_no')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('bills', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('amount');
            }
            
            if (!Schema::hasColumn('bills', 'total_price')) {
                $table->decimal('total_price', 10, 2)->nullable()->after('discount');
            }
            
            if (!Schema::hasColumn('bills', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('total_price');
            }
            
            if (!Schema::hasColumn('bills', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_type');
            }
            
            if (!Schema::hasColumn('bills', 'due_amount')) {
                $table->decimal('due_amount', 10, 2)->default(0)->after('paid_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'all_test')) {
                $table->dropColumn('all_test');
            }
            if (Schema::hasColumn('bills', 'bill_no')) {
                $table->dropColumn('bill_no');
            }
            if (Schema::hasColumn('bills', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('bills', 'total_price')) {
                $table->dropColumn('total_price');
            }
            if (Schema::hasColumn('bills', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('bills', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
            if (Schema::hasColumn('bills', 'due_amount')) {
                $table->dropColumn('due_amount');
            }
        });
    }
};
