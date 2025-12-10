<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatesToDatetimeInPatientsTable extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Change from date to datetime to store time information
            $table->dateTime('receiving_date')->nullable()->change();
            $table->dateTime('reporting_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Revert back to date if needed
            $table->date('receiving_date')->nullable()->change();
            $table->date('reporting_date')->nullable()->change();
        });
    }
}
