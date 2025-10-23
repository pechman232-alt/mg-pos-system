<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_reports', function (Blueprint $table) {
            $table->string('price')->nullable()->change();
            $table->string('pro_offer_oil_date')->nullable()->change();
            $table->string('feedback_command', 1000)->nullable()->after('follow_up');
            $table->unsignedInteger('warranty_leap_motor')->nullable()->after('warranty_id');
        });
    }

    public function down(): void
    {
        Schema::table('sale_reports', function (Blueprint $table) {

        });
    }
};
