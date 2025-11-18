<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakePatientIdNullable extends Migration
{
    public function up()
    {
        // Use raw SQL to alter the column to be nullable to support model-created assignment
        // on the 'created' event which occurs after the initial insert.
        DB::statement("ALTER TABLE patients MODIFY patient_id VARCHAR(255) NULL");
    }

    public function down()
    {
        // Revert back to not-null (caution: this will fail if there are null patient_id rows)
        DB::statement("ALTER TABLE patients MODIFY patient_id VARCHAR(255) NOT NULL");
    }
}
