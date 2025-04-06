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
            $table->string('id', 255)->collation('utf8mb4_unicode_ci')->primary();
            $table->string('title'); // Product title
            $table->text('description'); // Product description
            $table->string('main_category'); // Main category (e.g., "Books")
            $table->integer('min_stock')->default(0); // Minimum Stock Threshold
            $table->integer('max_stock')->nullable(); // Maximum Stock Threshold
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
