<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventorieshistoryTable extends Migration
{
    public function up()
    {
        Schema::create('inventorieshistory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventories_id');
            $table->integer('changed_quantity');
            $table->string('action'); // Added / Removed
            $table->timestamps();

            $table->foreign('inventories_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventorieshistory');
    }
}
