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
        // Add new datetime columns
        Schema::table('patients', function (Blueprint $table) {
            $table->dateTime('receiving_datetime')->nullable()->after('receiving_date');
            $table->dateTime('reporting_datetime')->nullable()->after('reporting_date');
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
