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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('sales_person'); 
            $table->string('branch'); 
            $table->timestamp('remind_date')->nullable(); // ✨ សម្រាប់ Auto-remind 7 ថ្ងៃ
            $table->string('customer_name');
            $table->string('contact_info')->unique(); 
            $table->string('location')->nullable();
            $table->string('source')->nullable();
            $table->string('model');
            $table->string('status')->default('Prospecting'); // ✨ ជំហានទី ១ នៃ Workflow
            $table->text('comment')->nullable();
            $table->boolean('is_transferred')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
