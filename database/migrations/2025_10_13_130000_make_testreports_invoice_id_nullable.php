<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        // Drop existing foreign key if it exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'testreports' 
            AND COLUMN_NAME = 'invoice_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName]);

        if (!empty($foreignKeys)) {
            $fkName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE `testreports` DROP FOREIGN KEY `{$fkName}`");
        }

        // Make column nullable - use BIGINT UNSIGNED to match bills.id (Laravel default)
        DB::statement("ALTER TABLE `testreports` MODIFY `invoice_id` BIGINT UNSIGNED NULL");

        // Recreate foreign key with onDelete set null
        DB::statement("
            ALTER TABLE `testreports` 
            ADD CONSTRAINT `testreports_invoice_id_foreign` 
            FOREIGN KEY (`invoice_id`) 
            REFERENCES `bills` (`id`) 
            ON DELETE SET NULL
        ");
    }

    public function down(): void
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'testreports' 
            AND COLUMN_NAME = 'invoice_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName]);

        if (!empty($foreignKeys)) {
            $fkName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE `testreports` DROP FOREIGN KEY `{$fkName}`");
        }
        
        DB::statement("ALTER TABLE `testreports` MODIFY `invoice_id` BIGINT UNSIGNED NOT NULL");
        
        DB::statement("
            ALTER TABLE `testreports` 
            ADD CONSTRAINT `testreports_invoice_id_foreign` 
            FOREIGN KEY (`invoice_id`) 
            REFERENCES `bills` (`id`) 
            ON DELETE CASCADE
        ");
    }
};