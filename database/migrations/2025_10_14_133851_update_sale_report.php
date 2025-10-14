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
        Schema::table('sale_reports', function (Blueprint $table) {
            $table->string('plate_number', 10)->change();
            $table->string('offer_license_plate', 30)->change();
            $table->string('offer_floor_mat', 30)->change();
            $table->string('side_step', 30)->change();
            $table->string('auto_tailgate', 30)->change();
            $table->string('rear_spoiler', 30)->change();
            $table->string('pro_offer_oil', 20)->change();
            $table->string('pro_offer_oil_date', 50)->change();    
            $table->string('price', 10)->change();    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_reports', function (Blueprint $table) {
            //
        });
    }
};
