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
        Schema::create('inventory_levels_report', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('location_id'); // Location ID (foreign key to locations table)
            $table->date('scan_date'); // Date of the scan
            $table->integer('detected_capacity'); // Detected capacity
            $table->enum('status', ['overstock', 'understock','normal']);
            $table->timestamps(); // Adds `created_at` and `updated_at` columns

            // Add foreign key constraint for location_id
            $table->foreign('location_id')
                  ->references('id')
                  ->on('locations')
                  ->onDelete('cascade'); // Optional: Delete related records if location is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_levels_report');
    }
};
