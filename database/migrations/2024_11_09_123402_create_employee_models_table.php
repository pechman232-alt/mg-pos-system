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
            $table->string('image_employee', 255)->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            
            // ដំឡើងប្រវែងឈ្មោះ ដើម្បីការពារឈ្មោះវែងៗ
            $table->string('full_name', 255)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('first_name_kh', 100)->nullable();
            $table->string('last_name_kh', 100)->nullable();
            
            $table->string('gender', 20)->nullable();
            $table->integer('age')->nullable();
            $table->date('dob')->nullable();
            $table->string('user_id', 50)->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->date('contract')->nullable();

            // --- គ្រាប់ Password និង ID ផ្សេងៗ ត្រូវដំឡើងឱ្យដល់ ២៥៥ ដើម្បីកុំឱ្យ Error ---
            $table->string('official_userid', 100)->nullable();
            $table->string('official_pass', 255)->nullable(); // កែពី 15 ទៅ 255
            $table->string('group_domain', 100)->nullable();
            
            $table->string('s_user_id', 100)->nullable();
            $table->string('s_pass', 255)->nullable(); // កែពី 15 ទៅ 255
            
            $table->string('m_user_id', 100)->nullable();
            $table->string('m_pass', 255)->nullable(); // កែពី 15 ទៅ 255
            
            $table->string('hr_user_id', 100)->nullable();
            $table->string('hr_pass', 255)->nullable(); // កែពី 15 ទៅ 255
            
            $table->string('rapp_user_id', 100)->nullable();
            $table->string('rapp_pass', 255)->nullable(); // កែពី 15 ទៅ 255
            
            $table->string('user_id_data_sheet', 100)->nullable();
            $table->string('data_sheet_pass', 255)->nullable(); // កែពី 20 ទៅ 255
            
            $table->string('extension_id', 50)->nullable(); // កែពី integer ទៅ string ដើម្បីងាយស្រួល
            $table->string('register_pass', 255)->nullable(); // កែពី 15 ទៅ 255
            
            $table->text('note')->nullable(); // កែពី string ទៅ text ដើម្បីវាយ Note បានវែង
            $table->date('activate')->nullable();
            $table->date('deactivate')->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('access_door', 100)->nullable(); // កែពី integer ទៅ string
            
            $table->integer('status')->default(1);
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