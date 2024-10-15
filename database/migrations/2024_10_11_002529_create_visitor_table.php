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
        Schema::connection('pgsql')->create('visitor', function (Blueprint $table) {
            $table->id('visitor_id', 255);
            $table->string('visitor_name', 255)->nullable();
            $table->string('visitor_from', 255)->nullable();

            // Foreign key column name from table employee
            $table->string('visitor_host', 255);
            $table->foreign('visitor_host')->references('name')->on('employee')->onDelete('cascade');

            $table->string('visitor_needs', 255)->nullable();
            $table->integer('visitor_amount')->nullable();
            $table->string('visitor_vehicle', 10)->nullable();
            $table->string('visitor_img', 255)->nullable();
            $table->dateTime('visitor_checkin');
            $table->dateTime('visitor_checkout');
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
