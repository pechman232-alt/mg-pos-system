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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('model_id');
            $table->unsignedInteger('offer_id');
            $table->string('name_en', 40);
            $table->string('name_kh', 40);
            $table->double('amount', 10, 2);
            $table->double('deposit', 10, 2);
            $table->double('balance', 10, 2);
            $table->string('payment_type', 15);
            $table->integer('special_discount')->nullable();
            $table->string('contract_number', 40)->nullable();
            $table->string('phone_number', 45)->nullable();
            $table->date('date_order')->nullable();
            $table->string('status', 15); // Delivered or In Stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
