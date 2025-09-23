<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyactivitiesTable extends Migration
{
    public function up()
    {
       Schema::create('dailyactivities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->date('date');   // ✅ Add this column
    $table->text('activity')->nullable();
    $table->timestamps();
});


    }

    public function down()
    {
        Schema::dropIfExists('dailyactivities');
    }
}
