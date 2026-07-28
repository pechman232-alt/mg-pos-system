<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->id();
            
            // 01. Showroom & Logistics
            $table->string('contract_number')->unique(); 
            $table->date('order_date'); 
            $table->string('sale_person'); 
            $table->string('branch_name'); 
            $table->string('source_id')->nullable(); 
            $table->string('membership_number')->nullable(); // 🌟 ត្រូវថែមដើម្បីឱ្យ Model ដើរ
            
            // 02. Status & Customer
            $table->enum('status', ['Booking', 'Payoff'])->default('Booking');
            $table->string('customer_name'); 
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('customer_id_card')->nullable(); 
            $table->text('address')->nullable();
            $table->string('location')->nullable(); 
            
            // 03. Vehicle Configuration
            $table->string('model'); 
            $table->string('color');
            $table->integer('qty')->default(1);
            $table->json('offers')->nullable(); 
            $table->string('vin_number')->unique()->nullable(); 
            $table->string('engine_number')->unique()->nullable(); 
            $table->string('plate_no')->nullable(); // 🌟 ថែមទុកសម្រាប់ពេលឡានចេញស្លាកលេខ
            $table->string('miles')->nullable();    // 🌟 ថែមទុកសម្រាប់ត្រួតពិនិត្យគីឡូឡាន
            
            // 04. Financials (Precision 15, 2)
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('deposit', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0); 
            $table->string('payment_type')->nullable(); 
    
            $table->timestamps();
    
            // 🚀 Index
            $table->index(['customer_name', 'phone_number', 'contract_number']);
            $table->index(['vin_number', 'engine_number']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_contracts');
    }
};
