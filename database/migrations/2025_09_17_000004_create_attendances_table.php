<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');

    // Fix: add columns used in controller
    $table->date('enter_date')->nullable();
    $table->time('enter_time')->nullable();
    $table->date('exit_date')->nullable();
    $table->time('exit_time')->nullable();

    $table->enum('status', ['Present', 'Absent', 'Leave'])->default('Absent');

    $table->timestamps();
});


    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
