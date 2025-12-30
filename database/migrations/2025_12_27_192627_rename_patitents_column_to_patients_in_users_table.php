<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePatitentsColumnToPatientsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This migration is no longer needed as the column is already correctly named
        // Schema::table('users', function (Blueprint $table) {
        //     $table->renameColumn('patitents', 'patients');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration is no longer needed as the column is already correctly named
        // Schema::table('users', function (Blueprint $table) {
        //     $table->renameColumn('patients', 'patitents');
        // });
    }
}
