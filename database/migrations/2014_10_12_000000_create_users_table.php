<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('role')->default('employee'); // 'admin' or 'employee'
            $table->string('phone_number')->nullable();
            $table->date('hire_date');
            // $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
            $table->string('supervisor_id')->nullable();;

            $table->rememberToken();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
