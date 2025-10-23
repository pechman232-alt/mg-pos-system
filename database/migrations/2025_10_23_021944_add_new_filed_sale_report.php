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
            $table->string('created_by', 100)->after('status');
            $table->string('updated_by', 100)->after('created_by');
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
