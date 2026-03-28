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
    Schema::create('lead_activities', function (Blueprint $table) {
        $table->id();
        
        // 🔗 តភ្ជាប់ទៅកាន់ Leads (បើលុប Lead វានឹងលុប Activity តាមក្រោយ - cascade)
        $table->foreignId('lead_id')->constrained()->onDelete('cascade');
        
        // 👤 តភ្ជាប់ទៅកាន់បុគ្គលិកដែលជាអ្នកធ្វើសកម្មភាព (User ID)
        $table->foreignId('user_id')->constrained('users'); 

        // 🏷 ព័ត៌មាននៃសកម្មភាព
        $table->string('status');               // ស្ថានភាពដែលបានប្តូរទៅ (ឧ. Engaged, Booked)
        $table->text('comment')->nullable();    // អ្វីដែលបុគ្គលិកបានសរសេរចំណាំទុក
        
        $table->timestamps();                   // នឹងបង្កើត created_at (សម្រាប់ដឹងម៉ោង Sync/Edit)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
