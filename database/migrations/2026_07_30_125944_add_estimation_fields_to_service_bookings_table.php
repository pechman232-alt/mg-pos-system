<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dateTime('estimated_end_datetime')->nullable()->after('end_time');
            $table->json('estimation_history')->nullable()->after('estimated_end_datetime');
        });
    }
    

    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn(['estimated_end_datetime', 'estimation_history']);
        });
    }
};
