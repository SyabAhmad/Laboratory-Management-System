<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionPercentageToReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrals', function (Blueprint $table) {
            if (!Schema::hasColumn('referrals', 'commission_percentage')) {
                $table->decimal('commission_percentage', 5, 2)->default(0)->after('phone')
                    ->comment('Commission percentage for each test/bill (0-100)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referrals', function (Blueprint $table) {
            if (Schema::hasColumn('referrals', 'commission_percentage')) {
                $table->dropColumn('commission_percentage');
            }
        });
    }
}
