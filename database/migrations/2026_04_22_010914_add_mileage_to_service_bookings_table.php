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
            // បន្ថែម mileage ជាប្រភេទលេខ (integer)
            // nullable() គឺដើម្បីកុំឱ្យ Error ជាមួយទិន្នន័យចាស់ៗដែលអត់មានគីឡូម៉ែត្រ
            // after('plate') គឺដើម្បីឱ្យវារៀបចំ Column នេះនៅបន្ទាប់ពី Column ស្លាកលេខ
            $table->integer('mileage')->nullable()->after('plate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            // លុបវាចេញវិញ ក្នុងករណីយើងចង់ Rollback
            $table->dropColumn('mileage');
        });
    }
};