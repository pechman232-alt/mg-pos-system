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
    Schema::create('pos_companies', function (Blueprint $table) {
        $table->id();
        $table->string('company_name_kh'); // ឈ្មោះខ្មែរ
        $table->string('company_name_en'); // ឈ្មោះអង់គ្លេស
        $table->string('logo')->nullable();   // ផ្លូវទុក Logo
        $table->string('vat_tin')->nullable(); // លេខ VAT
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->text('address')->nullable();
        $table->string('website')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_companies');
    }
};
