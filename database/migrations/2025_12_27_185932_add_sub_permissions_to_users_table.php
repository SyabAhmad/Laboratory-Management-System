<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubPermissionsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Billing sub-permissions
            $table->boolean('billing_add')->default(0);
            $table->boolean('billing_edit')->default(0);
            $table->boolean('billing_delete')->default(0);

            // Employees sub-permissions
            $table->boolean('employees_add')->default(0);
            $table->boolean('employees_edit')->default(0);
            $table->boolean('employees_delete')->default(0);

            // Pathology sub-permissions
            $table->boolean('pathology_add')->default(0);
            $table->boolean('pathology_edit')->default(0);
            $table->boolean('pathology_delete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['billing_add', 'billing_edit', 'billing_delete', 'employees_add', 'employees_edit', 'employees_delete', 'pathology_add', 'pathology_edit', 'pathology_delete']);
        });
    }
}
