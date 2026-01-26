<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardQueryIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->index('created_at', 'bills_created_at_index');
            $table->index('updated_at', 'bills_updated_at_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('created_at', 'payments_created_at_index');
        });

        Schema::table('referral_commissions', function (Blueprint $table) {
            $table->index('updated_at', 'referral_commissions_updated_at_index');
            $table->index(['status', 'updated_at'], 'referral_commissions_status_updated_at_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date', 'expenses_expense_date_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropIndex('bills_created_at_index');
            $table->dropIndex('bills_updated_at_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_created_at_index');
        });

        Schema::table('referral_commissions', function (Blueprint $table) {
            $table->dropIndex('referral_commissions_updated_at_index');
            $table->dropIndex('referral_commissions_status_updated_at_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_expense_date_index');
        });
    }
}
