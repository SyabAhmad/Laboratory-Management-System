<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanysTable extends Migration
{
    public function up()
    {
        Schema::create('companys', function (Blueprint $table) {
            $table->id();
            $table->string('lab_name');
            $table->string('lab_address')->nullable();
            $table->string('lab_phone')->nullable();
            $table->string('lab_email')->nullable();
            $table->decimal('balance', 12, 2)->default(0); 
            $table->string('lab_image')->nullable();       
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companys');
    }
}
