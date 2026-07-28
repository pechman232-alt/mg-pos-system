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
    Schema::table('users', function (Blueprint $table) {
        // បន្ថែមលេខទូរស័ព្ទ (Phone Number) តែត្រូវប្រាកដថាមិនជាន់គ្នា (Unique)
        $table->string('phone')->unique()->nullable();
        
        // បន្ថែមកន្លែងផ្ទុកកូដ OTP បណ្តោះអាសន្ន
        $table->string('otp_code')->nullable();
        
        // កំណត់ម៉ោងផុតកំណត់របស់ OTP (ឧទាហរណ៍ ៥ នាទី)
        $table->timestamp('otp_expires_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
