<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->string('id')->primary(); // Location ID (e.g., "L0001")
            $table->string('zone_name'); // Name of the zone
            $table->string('aisle'); // Aisle number
            $table->string('rack'); // Rack number
            $table->integer('capacity'); // Total capacity
            $table->integer('current_capacity'); // Current capacity
            $table->timestamps(); // Adds `created_at` and `updated_at` columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
