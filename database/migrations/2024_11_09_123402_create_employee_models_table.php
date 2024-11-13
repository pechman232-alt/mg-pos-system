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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('position_id');
            $table->string('full_name', 50);
            $table->string('first_name', 20);
            $table->string('last_name', 30);
            $table->string('first_name_kh', 30);
            $table->string('last_name_kh', 30);
            $table->string('gender', 6);
            $table->integer('age', 2);
            $table->date('dob')->nullable();
            $table->string('user_id', 20);
            $table->unsignedInteger('branch_id');
            $table->date('contract');
            $table->string('official_userid', 40)->nullable();
            $table->string('official_pass', 15)->nullable();
            $table->string('group_domain', 15)->nullable();
            $table->string('s_user_id', 40)->nullable();
            $table->string('s_pass', 15)->nullable();
            $table->string('m_user_id', 50)->nullable();
            $table->string('m_pass', 15)->nullable();
            $table->string('hr_user_id', 20)->nullable();
            $table->string('hr_pass', 15)->nullable();
            $table->string('rapp_user_id', 15)->nullable();
            $table->string('rapp_pass', 15)->nullable();
            $table->string('user_id_data_sheet', 20)->nullable();
            $table->string('data_sheet_pass', 20)->nullable();
            $table->ingetger('extension_id', 4)->nullable();
            $table->string('register_pass', 15)->nullable();
            $table->string('note', 50)->nullable();
            $table->date('activate', 15)->nullable();
            $table->date('deactivate', 15)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->integer('access_door')->nullable();
            $table->integer('status');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
