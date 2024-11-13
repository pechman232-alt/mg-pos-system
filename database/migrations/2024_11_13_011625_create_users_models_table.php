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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 20);
            $table->string('username', 50);
            $table->string('full_name',50);
            $table->string('gender', 6);
            $table->string('email', length: 50)->unique();
            $table->unsignedInteger('branch_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger(column: 'position_id');
            $table->string('phone_number', 20)->nullable();
            $table->string('profile_pic','150')->nullable();
            $table->date('start_work_date')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
