<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->create('employee', function (Blueprint $table) {
            $table->string('name', 255)->primary();
            $table->string('email', 255)->unique();
            $table->string('department', 20)->nullable();
            $table->integer('phone_number')->nullable();
            $table->string('employee_code', 3)->nullable();
            $table->integer('nik')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
