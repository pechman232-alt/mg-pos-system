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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            // បង្កើត Foreign Key ភ្ជាប់ទៅក្បាលវិក្កយបត្រ
            $table->foreignId('sales_header_id')->constrained('sales_headers')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id');
            $table->string('item_code'); // រក្សាទុក Code ទោះទំនិញត្រូវលុបក៏នៅមានប្រវត្តិ
            $table->string('item_name');
            
            $table->integer('qty');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2); // qty * unit_price
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
