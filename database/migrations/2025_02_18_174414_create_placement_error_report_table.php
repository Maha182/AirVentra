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
        Schema::create('placement_error_report', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('product_id', 255)->collation('utf8mb4_unicode_ci');
            $table->date('scan_date'); // Date of the scan
            $table->string('wrong_location'); // Incorrect location ID
            $table->string('correct_location'); // Correct location ID
            $table->string('status'); // Status of the report
            $table->timestamps(); // Adds `created_at` and `updated_at` columns

            // Add foreign key constraint for product_id
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade'); // Optional: Delete related records if product is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_error_report');
    }
};
