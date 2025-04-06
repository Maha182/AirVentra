<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->enum('error_type', ['misplaced', 'capacity']);
            $table->unsignedBigInteger('error_id');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->datetime('deadline')->nullable(); // Added deadline column
            $table->datetime('assigned_at')->default(DB::raw('CURRENT_TIMESTAMP')); // Track when task was assigned
            $table->datetime('completed_at')->nullable(); // Track when task was completed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
