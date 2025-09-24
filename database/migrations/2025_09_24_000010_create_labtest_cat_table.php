<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabtestCatTable extends Migration
{
    public function up()
    {
        Schema::create('labtest_cat', function (Blueprint $table) {
            $table->id();
            $table->string('cat_name');
            $table->string('department')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('labtest_cat');
    }
}