<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgePartsToPatientsTable extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Add integer components of age — nullable for backward compatibility
            $table->unsignedSmallInteger('age_years')->nullable()->after('age');
            $table->unsignedTinyInteger('age_months')->nullable()->after('age_years');
            $table->unsignedTinyInteger('age_days')->nullable()->after('age_months');
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['age_years', 'age_months', 'age_days']);
        });
    }
}
