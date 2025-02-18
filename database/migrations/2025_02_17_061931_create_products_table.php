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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Product title
            $table->text('description'); // Product description
            $table->string('main_category'); // Main category (e.g., "Books")
            $table->integer('quantity'); // Quantity available
            $table->string('location_id')->nullable(); // Location ID (nullable)
            $table->string('barcode_path'); // Path to barcode image
            $table->timestamps(); // Adds `created_at` and `updated_at` columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
