<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatetimeColumnsToPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new datetime columns only if they don't exist
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'receiving_datetime')) {
                $table->dateTime('receiving_datetime')->nullable()->after('receiving_date');
            }
            if (!Schema::hasColumn('patients', 'reporting_datetime')) {
                $table->dateTime('reporting_datetime')->nullable()->after('reporting_date');
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
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('receiving_datetime');
            $table->dropColumn('reporting_datetime');
        });
    }
}
