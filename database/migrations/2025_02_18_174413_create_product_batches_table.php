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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id(); // Auto-increment Batch ID
            $table->string('product_id', 255)->collation('utf8mb4_unicode_ci'); // Matches products.id type
            $table->string('barcode')->unique(); // Unique barcode for the batch
            $table->integer('quantity'); // Quantity in the batch
            $table->date('expiry_date')->nullable(); // Expiry date (if applicable)
            $table->date('received_date'); // Date the batch was received
            $table->string('location_id')->nullable(); // Location ID (nullable)
            $table->string('status')->default('in_stock'); // (e.g., "in_stock", "reserved", "sold")
            $table->timestamps(); // Created at & Updated at

            // Foreign key constraint
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
            
            // Add index for better performance
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('product_batches');
    }
};
