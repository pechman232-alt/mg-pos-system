<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->date('est_finish_date')->nullable(); // ថ្ងៃរំពឹងថានឹងរួចរាល់
            $table->text('mechanic_remark')->nullable(); // មតិយោបល់មេជាង
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
