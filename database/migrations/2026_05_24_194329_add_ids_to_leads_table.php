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
        Schema::table('leads', function (Blueprint $table) {
            // បន្ថែមសោរសម្រាប់កំណត់ម្ចាស់ទិន្នន័យ (ID)
            $table->unsignedBigInteger('sales_person_id')->nullable()->after('id');
            // បន្ថែមសោរសម្រាប់កំណត់សាខា (Branch ID)
            $table->unsignedBigInteger('branch_id')->nullable()->after('sales_person_id');
            
            // បង្កើត Index ដើម្បីឱ្យការ Query លឿន (Performance Optimization)
            $table->index('sales_person_id');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['sales_person_id', 'branch_id']);
        });
    }
};