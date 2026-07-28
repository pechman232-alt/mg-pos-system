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
        Schema::create('sales_headers', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // លេខវិក្កយបត្រ (មិនឱ្យស្ទួន)
            $table->dateTime('sale_date');          // កាលបរិច្ឆេទលក់
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            
            // ភ្ជាប់ទៅកាន់សាខា (Location/Branch)
            $table->unsignedBigInteger('location_id'); 
            
            // ផ្នែកតួលេខ (គណនេយ្យ)
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0); // ពន្ធជា %
            $table->decimal('tax_amount', 15, 2)->default(0);  // ចំនួនទឹកប្រាក់ពន្ធ
            $table->decimal('grand_total', 15, 2)->default(0); // សរុបទឹកប្រាក់ចុងក្រោយ
            
            $table->string('payment_method')->default('Cash'); // Cash, ABA, etc.
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_headers');
    }
};
