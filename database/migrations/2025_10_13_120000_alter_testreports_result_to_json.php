<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('testreports')) {
            return;
        }

        try {
            // Try JSON type first
            DB::statement("ALTER TABLE `testreports` MODIFY `result` JSON NULL");
        } catch (\Throwable $e) {
            // Fallback for MariaDB/older MySQL: store JSON as LONGTEXT
            DB::statement("ALTER TABLE `testreports` MODIFY `result` LONGTEXT NULL");
        }
    }

    public function down(): void
    {
        // No-op: leave as JSON/LONGTEXT
    }
};