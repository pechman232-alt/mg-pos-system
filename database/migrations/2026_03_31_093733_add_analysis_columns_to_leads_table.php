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
            $table->text('concern')->nullable()->after('model');      // ថែម column concern
            $table->string('competition')->nullable()->after('concern'); // ថែម column competition
        });
    }
    
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['concern', 'competition']);
        });
    }
};
