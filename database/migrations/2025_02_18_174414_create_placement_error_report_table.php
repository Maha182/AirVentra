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

            // Product and batch reference
            $table->string('product_id', 255)->collation('utf8mb4_unicode_ci');
            $table->unsignedBigInteger('batch_id');
            $table->string('barcode'); // Scanned barcode

            // Placement error details
            $table->date('scan_date');
            $table->string('wrong_location'); // Incorrect location ID
            $table->string('correct_location'); // Correct location ID
            $table->string('status'); // e.g., Pending, Resolved

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            $table->foreign('batch_id')
                  ->references('id')
                  ->on('product_batches')
                  ->onDelete('cascade');

            // Indexes for performance
            $table->index('product_id');
            $table->index('batch_id');
            $table->index('barcode');
            $table->index('scan_date');
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
