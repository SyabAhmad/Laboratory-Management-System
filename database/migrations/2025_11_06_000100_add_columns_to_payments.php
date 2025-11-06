<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'reference')) {
                $table->string('reference')->nullable()->after('method');
            }
            if (!Schema::hasColumn('payments', 'note')) {
                $table->text('note')->nullable()->after('reference');
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
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('payments', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
}
