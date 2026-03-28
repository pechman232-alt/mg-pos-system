<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->id();
            
            // 1. Core Registry Logistics
            $table->string('contract_number')->unique(); // លេខឯកសារ
            $table->date('order_date'); // កាលបរិច្ឆេទ
            $table->string('sale_person'); // ផ្នែកលក់
            $table->string('branch_name'); // សាខា (Phnom Penh, Siem Reap, etc.)
            $table->string('source'); // ប្រភព (Walk-in, FB, etc.)
            
            // 2. Transaction Status
            // Handles the logic: Booking (Reserved) vs Payoff (Settled)
            $table->enum('status', ['Booking', 'Payoff'])->default('Booking');
            
            // 3. Customer Identity (Bilingual Support)
            $table->string('customer_name'); // ឈ្មោះអតិថិជន
            $table->string('phone_number'); // លេខទំនាក់ទំនង
            $table->string('email')->nullable(); // អ៊ីម៉ែល
            $table->string('customer_id_card')->nullable(); // អត្តសញ្ញាណប័ណ្ឌ
            $table->string('membership_number')->nullable(); // លេខកាតសមាជិក
            $table->text('address'); // អាសយដ្ឋានពេញ
            $table->string('location_province'); // ខេត្ត/ក្រុង (For filtering)
            
            // 4. Vehicle Specifications
            $table->string('model'); // ម៉ូឌែល
            $table->string('color'); // ពណ៌
            $table->integer('qty')->default(1); // បរិមាណ
            $table->text('offers')->nullable(); // ការផ្តល់ជូនបន្ថែម (Free Gifts)
            $table->string('vin_number')->nullable(); // លេខតួ
            $table->string('engine_number')->nullable(); // លេខម៉ាស៊ីន
            $table->string('plate_no')->nullable(); // ស្លាកលេខ
            $table->integer('miles')->nullable(); // គីឡូម៉ែត្រ
            
            // 5. Financial Audit (High Precision)
            $table->decimal('sub_total', 15, 2)->default(0); // តម្លៃលក់សរុប
            $table->decimal('discount', 15, 2)->default(0); // បញ្ចុះតម្លៃ
            $table->decimal('deposit', 15, 2)->default(0); // ប្រាក់កក់
            $table->decimal('grand_total', 15, 2)->default(0); // សមតុល្យដែលនៅសល់ (Balance Due)
            $table->string('payment_type'); // Cash (សាច់ប្រាក់) or Leasing (បង់រំលស់)

            $table->timestamps();
            
            // Indexing for faster search in Chrome
            $table->index(['customer_name', 'phone_number', 'contract_number']);
            $table->index('status');
            $table->index('branch_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('sales_contracts');
    }
};