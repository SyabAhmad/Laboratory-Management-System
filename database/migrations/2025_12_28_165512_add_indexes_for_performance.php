<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesForPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add index on cat_name for faster test category lookups
        Schema::table('labtest_cat', function (Blueprint $table) {
            $table->index('cat_name');
        });

        // Add index on lab_test_cat_id for faster parameter lookups
        Schema::table('lab_test_parameters', function (Blueprint $table) {
            $table->index('lab_test_cat_id');
        });

        // Add index on parameter_name for faster parameter searches
        Schema::table('lab_test_parameters', function (Blueprint $table) {
            $table->index('parameter_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labtest_cat', function (Blueprint $table) {
            $table->dropIndex(['cat_name']);
        });

        Schema::table('lab_test_parameters', function (Blueprint $table) {
            $table->dropIndex(['lab_test_cat_id']);
            $table->dropIndex(['parameter_name']);
        });
    }
}
