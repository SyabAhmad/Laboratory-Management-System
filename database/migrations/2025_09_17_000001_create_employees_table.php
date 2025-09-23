<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('employee_id')->unique();
    $table->string('address')->nullable();
    $table->string('phone')->nullable();
    $table->string('image')->nullable();
    $table->date('dob')->nullable();
    $table->string('position')->nullable();
    $table->date('join_of_date')->nullable();
    $table->decimal('salary', 10, 2)->nullable();
    $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
