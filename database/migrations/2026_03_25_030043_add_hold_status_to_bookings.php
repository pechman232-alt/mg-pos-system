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
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change(); // ប្រាកដថាវាអាចទទួលតម្លៃថ្មីៗបាន
            $table->integer('total_seconds')->default(0); // ទុកចំនួនវិនាទីដែលបានធ្វើរួច
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            //
        });
    }
};
