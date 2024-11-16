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
        Schema::connection('mysql')->create('visitor', function (Blueprint $table) {
            $table->string('visitor_id', 255)->primary();
            $table->date('visitor_date');
            $table->string('visitor_name', 255)->nullable();
            $table->string('visitor_from', 255)->nullable();

            // Foreign key column name from table employee
            $table->string('visitor_host', 255);
            
            $table->string('visitor_needs', 255)->nullable();
            $table->integer('visitor_amount')->nullable();
            $table->string('visitor_vehicle', 10)->nullable();
            $table->string('department', 20)->nullable();
            // $table->string('visitor_img', 255)->nullable();
            $table->timestamp('visitor_checkin')->nullable();
            $table->timestamp('visitor_checkout')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor');
    }
};
