<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BackfillPatientIds extends Migration
{
    public function up()
    {
        // Backfill missing patient_id values using the year of created_at and numeric id
        // Resulting format: PT{YYYY}{padded_id} e.g. PT2025000001
        DB::statement("UPDATE patients SET patient_id = CONCAT('PT', COALESCE(YEAR(created_at), YEAR(CURDATE())), LPAD(id, 6, '0')) WHERE patient_id IS NULL OR patient_id = ''");
    }

    public function down()
    {
        // No-op rollback; do not remove existing patient IDs automatically.
    }
}
