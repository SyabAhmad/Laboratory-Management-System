<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'created_at')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->index('created_at', 'patients_created_at_index');
            });
        }

        if (Schema::hasTable('testreports')) {
            Schema::table('testreports', function (Blueprint $table) {
                if (Schema::hasColumn('testreports', 'status')) {
                    $table->index('status', 'testreports_status_index');
                }
                if (Schema::hasColumn('testreports', 'updated_at')) {
                    $table->index('updated_at', 'testreports_updated_at_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropIndex('patients_created_at_index');
            });
        }

        if (Schema::hasTable('testreports')) {
            Schema::table('testreports', function (Blueprint $table) {
                if (Schema::hasColumn('testreports', 'status')) {
                    $table->dropIndex('testreports_status_index');
                }
                if (Schema::hasColumn('testreports', 'updated_at')) {
                    $table->dropIndex('testreports_updated_at_index');
                }
            });
        }
    }
}
